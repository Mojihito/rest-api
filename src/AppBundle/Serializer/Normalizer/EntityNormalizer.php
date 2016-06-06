<?php
/**
 * This file is part of the rest-api package.
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Normalizer;

use AppBundle\Serializer\Exception\DepthException;
use AppBundle\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\{
    NormalizerInterface, ObjectNormalizer
};

/**
 * Class EntityNormalizer
 * @package AppBundle\Serializer\Normalizer
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class EntityNormalizer extends ObjectNormalizer
{
    const DEPTH_LEVEL = 1;
    const MAX_DEPTH_UNSET = -1;
    /**
     * @var array
     */
    protected $attributesCache = array();

    /**
     * @var callable
     */
    protected $maxDepthHandler;

    protected $helper;

    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null)
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor);

        $this->setCircularReferenceHandler(function ($object) {
            return method_exists($object, 'getId') ? $object->getId() : (string)$object;
        });

        $this->setMaxDepthHandler(function ($object) {

            if (method_exists($object, 'getId')) {
                return $object->getId();
            }

            if ($object instanceof \DateTime) {
                return $object->getTimestamp();
            }

            if (is_array($object)) {
                return $object;
            }

            return null;
        });
    }

    /**
     * Gets attributes to normalize using groups.
     *
     * @param string|object $classOrObject
     * @param array $context Context options for the normalizer
     *
     * @return string[]|AttributeMetadata[]|bool
     */
    protected function getMaxDepth($classOrObject, array $context)
    {
        $maxDepths = [];
        foreach ($this->classMetadataFactory->getMetadataFor($classOrObject)->getAttributesMetadata() as $attributeMetadata) {
            $depth = $attributeMetadata->getDepth();
            if ($depth > 0) {
                $maxDepths[$attributeMetadata->getName()] = $depth + $context['depth'];
            }
        }

        return $maxDepths;
    }

    /**
     * Handles max depth.
     *
     * @param object $object object to normalize
     * @param array $context Context options for the normalizer
     *
     * @return mixed
     *
     * @throws DepthException
     */
    protected function handleMaxDepth($object, array $context)
    {
        if ($this->maxDepthHandler) {
            return call_user_func($this->maxDepthHandler, $object);
        }

        throw new DepthException(sprintf('Max depth has been detected (configured limit: %d).', $context['max_depth']));
    }

    /**
     * Set max depth handler.
     *
     * @param callable $maxDepthHandler
     *
     * @return self
     */
    public function setMaxDepthHandler(callable $maxDepthHandler)
    {
        $this->maxDepthHandler = $maxDepthHandler;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if (!isset($context['cache_key'])) {
            $context['cache_key'] = $this->getCacheKey($context);
        }

        if (!isset($context['depth'])) {
            $context['depth'] = static::DEPTH_LEVEL;
        }

        if (!isset($context['max_depth'])) {
            $context['max_depth'] = static::MAX_DEPTH_UNSET;
        }

        if (!isset($context['max_depths'])) {
            $context['max_depths'] = [];
        }

        if ($context['max_depth'] === static::MAX_DEPTH_UNSET) {
            $context['max_depths'] = $this->getMaxDepth($object, $context);
        }

        if ($this->isCircularReference($object, $context)) {
            return $this->handleCircularReference($object);
        }

        $data = array();
        $attributes = $this->getAttributes($object, $context);

        foreach ($attributes as $attribute) {
            if (in_array($attribute, $this->ignoredAttributes)) {
                continue;
            }

            $attributeValue = $this->propertyAccessor->getValue($object, $attribute);

            if (isset($this->callbacks[$attribute])) {
                $attributeValue = call_user_func($this->callbacks[$attribute], $attributeValue);
            }

            $attributeValue = $attributeValue instanceof \DateTime
                ? $attributeValue->getTimestamp()
                : $attributeValue;

            if (null !== $attributeValue && !is_scalar($attributeValue)) {
                if (!$this->serializer instanceof NormalizerInterface) {
                    throw new LogicException(sprintf('Cannot normalize attribute "%s" because injected serializer is not a AppBundle\Serializer\Normalizer\ObjectNormalizer', $attribute));
                }

                if ($context['max_depth'] === static::MAX_DEPTH_UNSET && isset($context['max_depths'][$attribute])) {
                    $context['max_depth'] = $context['max_depths'][$attribute];
                }

                if ($context['max_depth'] !== static::MAX_DEPTH_UNSET && $context['depth'] >= $context['max_depth']) {
                    $attributeValue = $this->handleMaxDepth($attributeValue, $context);
                } else {
                    $newContext = $context;
                    $newContext['depth'] = $newContext['depth'] + 1;
                    $attributeValue = $this->serializer->normalize($attributeValue, $format, $newContext);
                }
            }

            if ($this->nameConverter) {
                $attribute = $this->nameConverter->normalize($attribute);
            }

            if ($attributeValue !== null) {
                $data[$attribute] = $attributeValue;
            }
        }

        return $data;
    }

    protected function getCacheKey(array $context)
    {
        try {
            return md5(serialize($context));
        } catch (\Exception $exception) {
            // The context cannot be serialized, skip the cache
            return false;
        }
    }

    /**
     * Gets and caches attributes for this class and context.
     *
     * @param object $object
     * @param array $context
     *
     * @return string[]
     */
    protected function getAttributes($object, array $context)
    {
        $class = get_class($object);
        $key = $class . '-' . $context['cache_key'];

        if (isset($this->attributesCache[$key])) {
            return $this->attributesCache[$key];
        }

        $allowedAttributes = $this->getAllowedAttributes($object, $context, true);

        if (false !== $allowedAttributes) {
            if ($context['cache_key']) {
                $this->attributesCache[$key] = $allowedAttributes;
            }

            return $allowedAttributes;
        }

        if (isset($this->attributesCache[$class])) {
            return $this->attributesCache[$class];
        }

        return $this->attributesCache[$class] = $this->extractAttributes($object);
    }

    /**
     * Extracts attributes for this class and context.
     *
     * @param object $object
     *
     * @return string[]
     */
    protected function extractAttributes($object)
    {
        // If not using groups, detect manually
        $attributes = array();

        // methods
        $reflClass = new \ReflectionClass($object);
        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            if (
                $reflMethod->getNumberOfRequiredParameters() !== 0 ||
                $reflMethod->isStatic() ||
                $reflMethod->isConstructor() ||
                $reflMethod->isDestructor()
            ) {
                continue;
            }

            $name = $reflMethod->name;

            if (0 === strpos($name, 'get') || 0 === strpos($name, 'has')) {
                // getters and hassers
                $attributes[lcfirst(substr($name, 3))] = true;
            } elseif (strpos($name, 'is') === 0) {
                // issers
                $attributes[lcfirst(substr($name, 2))] = true;
            }
        }

        // properties
        foreach ($reflClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflProperty) {
            if ($reflProperty->isStatic()) {
                continue;
            }

            $attributes[$reflProperty->name] = true;
        }

        return array_keys($attributes);
    }
}

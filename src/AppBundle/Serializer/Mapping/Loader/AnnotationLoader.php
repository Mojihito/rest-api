<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Mapping\Loader;

use AppBundle\Serializer\Annotation\Depth;
use AppBundle\Serializer\Mapping\AttributeMetadata;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Exception\MappingException;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

/**
 * Class AnnotationLoader
 * @package AppBundle\Serializer\Mapping\Loader
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class AnnotationLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        $reflectionClass = $classMetadata->getReflectionClass();
        $className = $reflectionClass->name;
        $loaded = false;

        $attributesMetadata = $classMetadata->getAttributesMetadata();

        foreach ($reflectionClass->getProperties() as $property) {
            if (!isset($attributesMetadata[$property->name])) {
                $attributesMetadata[$property->name] = new AttributeMetadata($property->name);
                $classMetadata->addAttributeMetadata($attributesMetadata[$property->name]);
            }

            if ($property->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $annotation) {
                    if ($annotation instanceof Groups) {
                        foreach ($annotation->getGroups() as $group) {
                            $attributesMetadata[$property->name]->addGroup($group);
                        }
                    } elseif ($annotation instanceof Depth) {
                        $attributesMetadata[$property->name]->setDepth($annotation->getDepth());
                    }

                    $loaded = true;
                }
            }
        }

        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->name === $className) {
                foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
                    if ($annotation instanceof Groups) {
                        if (preg_match('/^(get|is|has|set)(.+)$/i', $method->name, $matches)) {
                            $attributeName = lcfirst($matches[2]);

                            if (isset($attributesMetadata[$attributeName])) {
                                $attributeMetadata = $attributesMetadata[$attributeName];
                            } else {
                                $attributesMetadata[$attributeName] = $attributeMetadata = new AttributeMetadata($attributeName);
                                $classMetadata->addAttributeMetadata($attributeMetadata);
                            }

                            foreach ($annotation->getGroups() as $group) {
                                $attributeMetadata->addGroup($group);
                            }
                        } else {
                            throw new MappingException(sprintf('Groups on "%s::%s" cannot be added. Groups can only be added on methods beginning with "get", "is", "has" or "set".', $className, $method->name));
                        }

                    } elseif ($annotation instanceof Depth) {
                        if (preg_match('/^(get|is|has|set)(.+)$/i', $method->name, $matches)) {
                            $attributeName = lcfirst($matches[2]);

                            if (isset($attributesMetadata[$attributeName])) {
                                $attributeMetadata = $attributesMetadata[$attributeName];
                            } else {
                                $attributesMetadata[$attributeName] = $attributeMetadata = new AttributeMetadata($attributeName);
                                $classMetadata->addAttributeMetadata($attributeMetadata);
                            }

                            $attributeMetadata->setDepth($annotation->getDepth());
                        } else {
                            throw new MappingException(sprintf('Groups on "%s::%s" cannot be added. Groups can only be added on methods beginning with "get", "is", "has" or "set".', $className, $method->name));
                        }
                    }

                    $loaded = true;
                }
            }
        }

        return $loaded;
    }
}

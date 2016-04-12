<?php
/**
 * This file is part of the what2wear_api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class CircularNormalizer
 * @package AppBundle\Serializer\Normalizer
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CircularNormalizer extends ObjectNormalizer
{
    public function __construct(ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null)
    {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor);

        $this->setCircularReferenceHandler(function ($object) {
            return (string)$object;
        });
    }

}

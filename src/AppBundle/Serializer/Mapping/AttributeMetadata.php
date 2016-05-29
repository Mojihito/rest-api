<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Mapping;

/**
 * Class AttributeMetadata
 * @package AppBundle\Serializer\Mapping
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class AttributeMetadata extends \Symfony\Component\Serializer\Mapping\AttributeMetadata
{
    /**
     * @var string
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getDepth()} instead.
     */
    public $depth;

    /**
     * @return string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     * @return AttributeMetadata
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        $sleep = parent::__sleep();

        return array_merge($sleep, ['depth']);
    }
}

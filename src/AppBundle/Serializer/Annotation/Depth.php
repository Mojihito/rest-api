<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Annotation;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * Class Depth
 * @package AppBundle\Serializer\Annotation
 * @author Rafał Lorenz <vardius@gmail.com>
 *
 * Annotation class for @Depth().
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class Depth
{
    /**
     * @var int
     */
    private $depth;

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) || !$data['value']) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.', get_class($this)));
        }

        if (!is_int($data['value'])) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" must be an integer.', get_class($this)));
        }

        $this->depth = $data['value'];
    }

    /**
     * Gets depth.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }
}

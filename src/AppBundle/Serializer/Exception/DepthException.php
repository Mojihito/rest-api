<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Serializer\Exception;

use Symfony\Component\Serializer\Exception\RuntimeException;

/**
 * Class DepthException
 * @package AppBundle\Serializer\Exception
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DepthException extends RuntimeException
{
}

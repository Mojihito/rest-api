<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Mateusz Bosek <bosek.mateusz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Filter;

use Vardius\Bundle\ListBundle\Filter\Provider\FilterProvider;
use Vardius\Bundle\ListBundle\Filter\Types\Type\TextType;

/**
 * Class FileFilterProvider
 * @package AppBundle\Filter
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FileFilterProvider extends FilterProvider
{
    /**
     * @inheritDoc
     */
    public function build()
    {
        $this
            ->addFilter('name', TextType::class)
            ->addFilter('path', TextType::class);
    }
}

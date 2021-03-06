<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Filter;

use Vardius\Bundle\ListBundle\Filter\Provider\FilterProvider;
use Vardius\Bundle\ListBundle\Filter\Types\Type\{
    DateType, TextType, PropertyType
};

/**
 * Class UserFilterProvider
 * @package AppBundle\Filter
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UserFilterProvider extends FilterProvider
{
    /**
     * @inheritDoc
     */
    public function build()
    {
        $this
            ->addFilter('email', TextType::class)
            ->addFilter('name', TextType::class)
            ->addFilter('surname', TextType::class)
            ->addFilter('roles', TextType::class)
            ->addFilter('enabled', PropertyType::class)
            ->addFilter('dateFrom', DateType::class, [
                'condition' => 'gte',
                'field' => 'created',
            ])
            ->addFilter('dateTo', DateType::class, [
                'condition' => 'lte',
                'field' => 'created',
            ]);
    }
}

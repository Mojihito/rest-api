<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\ListView;

use AppBundle\Entity\User;
use AppBundle\Form\Type\Filter\UserFilterType;
use Vardius\Bundle\ListBundle\Column\Types\Type\{
    CallableType, PropertyType
};
use Vardius\Bundle\ListBundle\ListView\ListView;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProvider;

/**
 * Class UserListViewProvider
 * @package AppBundle\ListView
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UserListViewProvider extends ListViewProvider
{
    /**
     * @inheritDoc
     */
    public function buildListView():ListView
    {
        $listView = $this->listViewFactory->get();

        $listView
            ->addColumn('id', PropertyType::class)
            ->addColumn('username', PropertyType::class)
            ->addColumn('email', PropertyType::class)
            ->addColumn('name', PropertyType::class)
            ->addColumn('surname', PropertyType::class)
            ->addColumn('avatar', PropertyType::class)
            ->addColumn('enabled', PropertyType::class)
            ->addColumn('roles', PropertyType::class)
            ->addColumn('created', CallableType::class, [
                'callback' => function (User $user) {
                    $date = $user->getCreated();

                    return $date ? $date->getTimestamp() : $date;
                },
            ])
            ->addFilter(UserFilterType::class, 'provider.users_filter');

        return $listView;
    }
}

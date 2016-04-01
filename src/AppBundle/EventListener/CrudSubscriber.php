<?php
/**
 * This file is part of the what2wear_api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\ListView\ListView;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProvider;

/**
 * Class CrudSubscriber
 * @package AppBundle\EventListener
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudSubscriber implements EventSubscriberInterface
{
    /** @var  ContainerInterface */
    protected $container;
    /** @var  RequestStack */
    protected $request;

    /**
     * CrudSubscriber constructor.
     * @param ContainerInterface $container
     * @param RequestStack $request
     */
    public function __construct(ContainerInterface $container, RequestStack $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CrudEvents::CRUD_EXPORT => 'onCSV'
        );
    }

    public function onCSV(CrudEvent $event)
    {
        $request = $this->request->getMasterRequest();
        $path = trim($request->getBasePath(), '/');

        /** @var ListViewProvider $listView */
        $listViewProvider = $this->container->get($path . '.list_view');
        /** @var ListView $listView */
        $listView = $listViewProvider->buildListView();
        $listView->setPagination(false);

        $repository = $event->getSource();
        $listDataEvent = new ListDataEvent($repository, $request);

        $event->setData($listView->getData($listDataEvent, true, true));
    }
}

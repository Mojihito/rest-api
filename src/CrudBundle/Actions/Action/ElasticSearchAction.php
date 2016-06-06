<?php
/**
 * This file is part of the rest-api package.
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrudBundle\Actions\Action;

use Elastica\Filter\BoolFilter;
use Elastica\Query\{
    Filtered, MatchAll
};
use Symfony\Component\HttpFoundation\Response;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\{
    ActionEvent, CrudEvent, CrudEvents, ListDataEvent, ResponseEvent
};
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProviderInterface;

/**
 * ElasticSearchAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ElasticSearchAction extends ListAction
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event, string $format):Response
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        $request = $event->getRequest();

        $query = new MatchAll();
        $filter = new BoolFilter();
        $source = new Filtered($query, $filter);
        $listDataEvent = new ListDataEvent($source, $request);

        /** @var ListViewProviderInterface $listViewProvider */
        $listViewProvider = $controller->get(str_replace('s/', '_', trim($controller->getRoutePrefix(), '/')) . '.list_view');
        $listView = $listViewProvider->buildListView();

        $columns = $listView->getColumns();

        $results = $listView->getData($listDataEvent, true);
        $results = $this->parseResults($results, $columns, $format);

        $params = [
            'data' => $results,
        ];

        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($source, $controller, $paramsEvent);

        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_LIST, $crudEvent);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        return $responseHandler->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['groups' => ['list']]);
    }
}

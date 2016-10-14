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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\{
    Options, OptionsResolver
};
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\{
    ActionEvent, CrudEvent, CrudEvents, ResponseEvent
};
use Vardius\Bundle\ListBundle\Column\ColumnInterface;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProviderInterface;

/**
 * ListAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListAction extends Action\ListAction
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event, string $format):Response
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        $request = $event->getRequest();
        $source = $event->getDataProvider()->getSource();
        $listDataEvent = new ListDataEvent($source, $request);

        /** @var ListViewProviderInterface $listViewProvider */
        $listViewProvider = $controller->get(str_replace('s/', '_', trim($controller->getRoutePrefix(), '/')) . '.list_view');
        $listView = $listViewProvider->buildListView();

        if ($format === 'html') {
            $params = [
                'list' => $listView->render($listDataEvent),
                'title' => $listView->getTitle(),
            ];

            $routeName = $request->get('_route');
            if (strpos($routeName, 'export') !== false) {
                $params['ui'] = false;
            }
        } else {
            $results = $listView->getData($listDataEvent, true);
            $results = $this->parseResults($results, $listView->getColumns(), $format);

            $count = $request->get('count');
            if ($count === 'true') {
                $listView->setPagination(false);
                $query = $listView->getData($listDataEvent, true, true);
                $aliases = $query->getRootAliases();
                $alias = array_values($aliases)[0];
                $query = $query->select('count(' . $alias . '.id)');
                $newResults = [
                    'total' => $query->getQuery()->getSingleScalarResult(),
                    'items' => $results
                ];
                $results = $newResults;
            }

            $params = $results;
        }

        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($source, $controller, $paramsEvent);

        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_LIST, $crudEvent);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        return $responseHandler->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['groups' => ['list']]);
    }

    /**
     * @param array $results
     * @param ArrayCollection|ColumnInterface[] $columns
     * @param string $format
     * @return array
     */
    protected function parseResults(array $results, $columns, $format)
    {
        foreach ($results as $key => $result) {
            if (is_array($result)) {

                $results[$key] = $this->parseResults($result, $columns, $format);
            } elseif (method_exists($result, 'getId')) {
                $rowData = [];

                /** @var ColumnInterface $column */
                foreach ($columns as $column) {
                    $columnData = $column->getData($result, $format);
                    if ($columnData) {
                        $rowData[$column->getLabel()] = $columnData;
                    }
                }
                $results[$key] = $rowData;
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('defaults', function (Options $options, $previousValue) {
            return $options['rest_route'] ? $previousValue : [
                '_format' => 'html',
                'page' => 1,
                'limit' => null,
                'column' => null,
                'sort' => null,
            ];
        });

        $resolver->setDefault('requirements', function (Options $options) {
            return $options['rest_route'] ? [] : [
                'page' => '\d+',
                'limit' => '\d+',
            ];
        });

        $resolver->setDefault('pattern', function (Options $options) {
            return $options['rest_route'] ? '.{_format}' : '/list/{page}/{limit}/{column}/{sort}.{_format}';
        });

        $resolver->setDefault('parameters', [
            ['name' => 'page', 'dataType' => 'integer', "required" => false, 'description' => 'page value'],
            ['name' => 'limit', 'dataType' => 'integer', "required" => false, 'description' => 'limit value'],
            ['name' => 'column', 'dataType' => 'string', "required" => false, 'description' => 'sorts data by column name'],
            ['name' => 'sort', 'dataType' => 'string', "required" => false, 'description' => 'sort method (ASC|DESC)'],
            ['name' => 'count', 'dataType' => 'bool', "required" => false, 'description' => 'if set actions returns count of objects (filters are available)'],
        ]);
    }
}

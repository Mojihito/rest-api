<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) RafaĹ Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrudBundle\Routing;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Routing\RouteCollection;
use Vardius\Bundle\CrudBundle\Actions\ActionInterface;
use Vardius\Bundle\CrudBundle\Controller\CrudController;
use Vardius\Bundle\CrudBundle\Routing\CrudPool;
use Vardius\Bundle\ListBundle\Filter\Filter;
use Vardius\Bundle\ListBundle\Filter\Types\AbstractType;

/**
 * CrudLoader
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudLoader extends \Vardius\Bundle\CrudBundle\Routing\CrudLoader implements LoaderInterface
{
    protected $container;

    /**
     * @inheritDoc
     */
    public function __construct(CrudPool $pool, ContainerInterface $container)
    {
        parent::__construct($pool);
        $this->container = $container;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->pool->getControllers() as $controllerKey => $controller) {
            /** @var CrudController $controller */
            foreach ($controller->getActions() as $actionKey => $action) {
                /** @var ActionInterface $action */
                $options = $action->getOptions();

                $pattern = rtrim($controller->getRoutePrefix() . $options['pattern'], '/');

                $defaults = $options['defaults'];
                $defaults['_controller'] = $controllerKey . ':' . 'callAction';
                $defaults['_action'] = $actionKey;

                $doc = $this->getDoc($controller, $actionKey, $options);

                $route = new Route(
                    $pattern,
                    $defaults,
                    $options['requirements'],
                    $options['options'],
                    $options['host'],
                    $options['schemes'],
                    $options['methods'],
                    $options['condition']
                );
                $route->setDoc($doc);

                $routeSuffix = (empty($options['route_suffix']) ? $actionKey : $options['route_suffix']);
                $routes->add($controllerKey . '.' . $routeSuffix, $route);
            }
        }

        return $routes;
    }

    protected function getDoc(CrudController $controller, $actionKey, $options)
    {
        $matches = [];
        $section = $controller->getRoutePrefix();
        preg_match('/v([0-9]+)/', $section, $matches);
        $section = preg_replace('/v([0-9]+)/', '', $section);
        $section = ltrim($section, '/');

        $parameters = array_key_exists('parameters', $options) ? $options['parameters'] : [];
        $filters = ($actionKey === 'list' || $actionKey === 'export') ? $this->getFilters($section) : [];
        $form = $controller->getFormType();
        $input = $form ? get_class($form) : '';

        $config = [
            'resource' => true,
            'views' => [$matches[0]],
            'section' => ucwords(str_replace(['-', '/'], ' ', $section)),
            'description' => ucfirst($actionKey) . " action",
            'statusCodes' => [
                200 => "OK",
                201 => "Created",
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                500 => 'Internal Server Error',
            ],
            'filters' => $filters,
            'parameters' => $parameters
        ];

        $action = $controller->getAction($actionKey);
        $options = $action->getOptions();
        if (array_key_exists('checkAccess', $options) && array_key_exists('attributes', $options['checkAccess'])) {
            $config['authentication'] = true;
            $config['authenticationRoles'] = $options['checkAccess']['attributes'];
        }

        if ($actionKey === 'add' || $actionKey === 'edit') {
            $config['input'] = $input;
        }

        return new ApiDoc($config);
    }

    /**
     * @param $section
     * @return array
     */
    protected function getFilters($section)
    {
        $section = str_replace('s/', '_', $section);
        $section = preg_replace('~[^\\pL\d]+~u', '_', $section);
        $section = trim($section, '-');
        $section = strtolower($section);

        if (!$this->container->has('provider.' . $section . '_filter')) {
            return [];
        }

        if (!$this->container->has('form.type.' . $section . '_filter')) {
            return [];
        }

        /** FilterProvider */
        $provider = $this->container->get('provider.' . $section . '_filter');
        /** @var FormTypeInterface $form */
        $form = $this->container->get('form.type.' . $section . '_filter');

        $provider->build();
        $filters = $provider->getFilters();

        $docFilters = [];
        /**
         * @var string $key
         * @var Filter|callable $filter
         */
        foreach ($filters as $key => $filter) {
            $name = 'callable';
            if (!is_callable($filter) && $filter->getType() instanceof AbstractType) {
                $class = get_class($filter->getType());
                $name = strtolower(rtrim(substr($class, strrpos($class, '\\') + 1), 'Type'));
            }
            $docFilters[] = ['name' => $form->getBlockPrefix() . '[' . $key . ']', 'type' => $name];
        }

        return $docFilters;
    }
}

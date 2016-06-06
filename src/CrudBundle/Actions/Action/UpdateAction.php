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

use Symfony\Component\OptionsResolver\{
    Options, OptionsResolver
};

/**
 * UpdateAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UpdateAction extends \Vardius\Bundle\CrudBundle\Actions\Action\UpdateAction
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('parameters', [
            ['name' => 'data', 'dataType' => 'json', "required" => true, 'description' => 'Array of properties to change'],
        ]);

        $resolver->setDefault('parameters', function (Options $options) {
            $parameters = [];
            $parameters[] = ['name' => 'data', 'dataType' => 'json', "required" => true, 'description' => 'Array of properties to change'];
            foreach ($options['allow'] as $option) {
                $parameters[] = ['name' => $option, 'dataType' => 'property', "required" => false, 'description' => 'Add value under this key to data object'];
            }

            return $parameters;
        });
    }
}

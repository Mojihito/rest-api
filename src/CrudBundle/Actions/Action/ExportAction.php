<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrudBundle\Actions\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;

/**
 * ExportAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ExportAction extends Action\ExportAction
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('requirements', ['id' => '\d+']);
        $resolver->setDefault('pattern', '/export/{id}');
        $resolver->setDefault('parameters', [
            ['name' => 'id', 'dataType' => 'integer', "required" => true, 'description' => 'element id'],
        ]);
        $resolver->setDefault('defaults', [
            "id" => null,
            "type" => 'csv'
        ]);
    }
}

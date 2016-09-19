<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type;

use AppBundle\Form\EventListener\RemoveNotSubmittedFieldListener;
use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractType
 * @package AppBundle\Form\Type
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class AbstractType extends BaseAbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new RemoveNotSubmittedFieldListener());
    }
}

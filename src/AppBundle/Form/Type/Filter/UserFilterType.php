<?php
/**
 * This file is part of the rest-api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Form\Type\Filter;

use AppBundle\Form\Type\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, DateType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserFilterType
 * @package AppBundle\Form\Type\Filter
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UserFilterType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('email', TextType::class, [
                'required' => false
            ])
            ->add('name', TextType::class, [
                'required' => false
            ])
            ->add('surname', TextType::class, [
                'required' => false
            ])
            ->add('roles', TextType::class, [
                'required' => false,
                'mapped' => false
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false
            ])
            ->add('dateTo', DateType::class, [
                'input' => 'timestamp',
                'mapped' => false,
                'required' => false
            ])
            ->add('dateFrom', DateType::class, [
                'input' => 'timestamp',
                'mapped' => false,
                'required' => false
            ])
            ->setMethod('GET');
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'validation_group' => ['filter'],
        ]);
    }
}

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

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, DateType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package AppBundle\Form\Type
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class UserType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, ['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('surname', TextType::class, ['required' => false])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('birth', DateType::class, [
                'input' => 'timestamp',
                'required' => false
            ])
            ->add('avatar', EntityType::class, [
                'class' => 'AppBundle\Entity\File',
                'choice_label' => 'id'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'validation_group' => ['update', 'Profile'],
        ]);
    }
}

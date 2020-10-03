<?php

namespace App\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', EmailType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(),
                ],
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'constraints' => [
                    new NotBlank(),
                ],
            ],
        ]);

        $builder->add('roles', ChoiceType::class, [
            'label' => 'Roles',
            'multiple' => true,
            'expanded' => true,
            'choices' => $options['roles'],
            'choice_value' => function (string $role = null) {
                return $role ? $role : '';
            },
            'choice_label' => function (string $role = null) {
                return $role ? ucwords(strtolower(str_replace('_', ' ', $role))) : '';
            },
        ]);

        $builder->add('Create', SubmitType::class, [
            'attr' => [
                'class' => 'btn-primary btn',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'roles' => [],
        ]);
    }
}

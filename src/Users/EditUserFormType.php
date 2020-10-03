<?php

namespace App\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EditUserFormType extends AbstractType
{
    protected $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class);

        if ($builder->getData()->getId() === $this->token->getToken()->getUser()->getId()) {
            $builder->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => false,
                    'first_options' => [
                        'label' => 'Password',
                    ],
                    'second_options' => [
                        'label' => 'Repeat Password',
                    ],
                ]
            );
        }

        if (in_array('ROLE_ADMIN', $this->token->getToken()->getUser()->getRoles())) {
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
        }

        $builder->add('save', SubmitType::class, [
            'label' => 'Save',
            'attr' => [
                'class' => 'btn-primary',
            ],
        ]);

        $builder->add('delete', SubmitType::class, [
            'label' => 'Delete',
            'attr' => [
                'class' => 'btn-outline-primary ml1',
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

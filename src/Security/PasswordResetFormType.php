<?php

namespace App\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PasswordResetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', RepeatedType::class, [
            'required' => 'required',
            'type' => PasswordType::class,
            'invalid_message' => 'The passwords do not match',
            'first_options' => [
                'label' => 'New Password',
                'attr' => [
                    'class' => 'form-password form-control',
                ],
            ],
            'second_options' => [
                'label' => 'Repeat the new password',
                'attr' => [
                    'class' => 'form-password form-control',
                ],
            ],
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Submit',
            'attr' => [
                'class' => 'btn-primary btn pull-left mr1',
            ],
        ]);
    }
}

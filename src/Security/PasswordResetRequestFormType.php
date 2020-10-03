<?php

namespace App\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', EmailType::class, [
            'label' => false,
            'attr' => [
                'class' => 'input-lg',
                'autocomplete' => 'off',
                'placeholder' => 'Enter your email...',
            ],
            'constraints' => [
                new Email(),
                new NotBlank(),
            ],
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Send Instructions',
            'attr' => [
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}

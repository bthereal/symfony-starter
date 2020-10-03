<?php

namespace App\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('submit', SubmitType::class, [
            'label' => 'Delete User',
            'attr' => [
                'class' => 'btn-primary btn',
            ],
        ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => ['label' => 'form.new_password'],
            'second_options' => ['label' => 'form.new_password_confirm'],
            'invalid_message' => 'Les mots de passe ne correspondent pas.',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.'),
            ],
        ]);
    }
}

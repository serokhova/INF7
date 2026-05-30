<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PayRentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('period', TextType::class, [
            'label' => 'form.period',
            'help' => 'YYYY-MM',
            'attr' => ['placeholder' => date('Y-m')],
            'data' => date('Y-m'),
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex('/^\d{4}-\d{2}$/', message: 'Format attendu : YYYY-MM'),
            ],
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Expense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'form.category',
                'choices' => [
                    'expense.water' => 'water',
                    'expense.electricity' => 'electricity',
                    'expense.internet' => 'internet',
                    'expense.taxes' => 'taxes',
                    'expense.other' => 'other',
                ],
            ])
            ->add('label', TextType::class, ['label' => 'form.label'])
            ->add('amount', MoneyType::class, [
                'label' => 'form.amount',
                'currency' => 'EUR',
                'scale' => 2,
            ])
            ->add('period', TextType::class, [
                'label' => 'form.period',
                'help' => 'YYYY-MM',
                'attr' => ['placeholder' => date('Y-m')],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Expense::class]);
    }
}

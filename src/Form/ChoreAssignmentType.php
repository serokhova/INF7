<?php

namespace App\Form;

use App\Entity\ChoreAssignment;
use App\Entity\Household;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoreAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Household $household */
        $household = $options['household'];

        $builder
            ->add('task', EntityType::class, [
                'label' => 'form.task',
                'class' => Task::class,
                'choice_label' => 'title',
            ])
            ->add('assignedTo', EntityType::class, [
                'label' => 'form.assigned_to',
                'class' => User::class,
                'choice_label' => fn (User $u) => $u->getFullName(),
                'query_builder' => function ($repo) use ($household) {
                    return $repo->createQueryBuilder('u')
                        ->andWhere('u.household = :h OR u = :owner')
                        ->setParameter('h', $household)
                        ->setParameter('owner', $household->getOwner());
                },
            ])
            ->add('day', ChoiceType::class, [
                'label' => 'form.day',
                'choices' => [
                    'day.monday' => 'monday',
                    'day.tuesday' => 'tuesday',
                    'day.wednesday' => 'wednesday',
                    'day.thursday' => 'thursday',
                    'day.friday' => 'friday',
                    'day.saturday' => 'saturday',
                    'day.sunday' => 'sunday',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ChoreAssignment::class]);
        $resolver->setRequired(['household']);
    }
}

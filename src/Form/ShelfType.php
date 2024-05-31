<?php

namespace App\Form;

use App\Entity\Shelf;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShelfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('basketCapacity', NumberType::class, [
            'attr' => [
                'placeholder' => 'Number of baskets on this shelf',
            ],
            'constraints' => [
                new GreaterThan(['value' => 0]),
                new LessThan(['value' => 101])
            ]
        ])
        ->add('kgCapacity', NumberType::class, [            
            'attr' => [
                'placeholder' => 'Capacity in kilograms of this rack',
            ],
            'constraints' => [
                new GreaterThan(['value' => 0]),
                new LessThan(['value' => 101])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Shelf::class,
        ]);
    }
}

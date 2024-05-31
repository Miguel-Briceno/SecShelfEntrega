<?php

namespace App\Form;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\Shelf;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('numProduct', NumberType::class, [
            'attr' => [
                'placeholder' => 'Number of products on this basket',
            ],
            'constraints' => [
                new GreaterThan(['value' => 0]),
                new LessThan(['value' => 101])
            ]
        ])                  
        ->add('id_shelf_basket', EntityType::class, [
            'class' => Shelf::class,
            'choice_label' => 'id',
        ])
        ->add('id_basket_product', EntityType::class, [
            'class' => Product::class,
            'choice_label' => 'productName',
        ]);         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
        ]);
    }
}

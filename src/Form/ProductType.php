<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('productBrand', TextType::class, [
            'constraints' => [
                new Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'La marca del producto debe tener al menos {{ limit }} caracteres',
                    'maxMessage' => 'La marca del producto no puede tener más de {{ limit }} caracteres',
                ])
            ]
        ])
        ->add('productName', TextType::class, [
            'constraints' => [
                new Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'La marca del producto debe tener al menos {{ limit }} caracteres',
                    'maxMessage' => 'La marca del producto no puede tener más de {{ limit }} caracteres',
                ])
            ]
        ])
        ->add('productWeight', NumberType::class, [
            'attr' => [
                'placeholder' => 'Number of products on this basket',
            ],
            'constraints' => [
                new GreaterThan(['value' => 0]),
                new LessThan(['value' => 50])
            ]
        ]) 
        ->add('productModel', TextType::class, [
            'constraints' => [
                new Length([
                    'min' => 2,
                    'max' => 50,
                    'minMessage' => 'La marca del producto debe tener al menos {{ limit }} caracteres',
                    'maxMessage' => 'La marca del producto no puede tener más de {{ limit }} caracteres',
                ])
            ]
        ]);         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}

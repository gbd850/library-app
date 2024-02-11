<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Title',
                    'class' => 'text-lg border rounded p-2 m-2 min-w-full'
                ]
            ])
            ->add('author', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Author',
                    'class' => 'text-lg border rounded p-2 m-2 min-w-full'
                ]
            ])
            ->add('releaseYear', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Release year',
                    'class' => 'text-lg border rounded p-2 m-2 min-w-full'
                ]
            ])
            ->add('rating', NumberType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Rating',
                    'class' => 'text-lg border rounded p-2 m-2 min-w-full'
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Quantity',
                    'class' => 'text-lg border rounded p-2 m-2 min-w-full'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

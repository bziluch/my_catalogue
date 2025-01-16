<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Opis',
            ])
            ->add('pricingMin', NumberType::class, [
                'label' => 'Wycena - minimum',
                'required' => false,
                'attr' => [
                    'step' => '0.01',
                    'max' => '99999999.99'
                ]
            ])
            ->add('pricingMax', NumberType::class, [
                'label' => 'Wycena - maksimum',
                'required' => false,
                'attr' => [
                    'step' => '0.01',
                    'max' => '99999999.99'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Zapisz',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }

}
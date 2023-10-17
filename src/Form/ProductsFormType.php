<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => array(
                    'placeholder' => 'Enter title...',
                ),
                'label' => false,
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'attr' => array(
                    'placeholder' => 'Enter Description...'
                ),
                'label' => false,
                'required' => false,
            ])
            ->add('price', IntegerType::class, [
                'attr' => array(
                    'placeholder' => 'Enter Price...'
                ),
                'label' => false,
                'required' => false
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => array(
                    'placeholder' => 'Enter quantity...'
                ),
                'label' => false,
                'required' => false
            ])
            ->add('c_id', EntityType::class, [
                'class' => 'App\Entity\Categories',
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Categories',
            ])           
            ->add('thumbnail', FileType::class, array(
                'required' => false,
                'mapped' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}

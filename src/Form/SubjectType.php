<?php

namespace App\Form;

use App\Entity\Subject;
use App\Entity\Category;
use App\Form\ImageMultipleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SubjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ["class" => "form-control"]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'categoryType',
                'attr' => ["class" => "form-control"]
            ])
            ->add('description', TextareaType::class, [
                'attr' => ["class" => "form-control"]
            ])
            ->add('images', ImageMultipleType::class , [
                'mapped' => false,
                'attr' => ["class" => "form-control"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}

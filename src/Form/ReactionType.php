<?php

namespace App\Form;

use App\Entity\Reaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('likes', SubmitType::class, ['label' => 'Like'])
            ->add('loves', SubmitType::class, ['label' => 'Love'])
            ->add('dontLike', SubmitType::class, ['label' => "Don't like"])
            ->add('wow', SubmitType::class, ['label' => 'WOW'])
            ->add('funny', SubmitType::class, ['label' => 'Hahaha'])
            ->add('sad', SubmitType::class, ['label' => 'Sad'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reaction::class,
        ]);
    }
}

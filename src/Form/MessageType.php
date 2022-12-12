<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Message;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['id'];
        $builder
            ->add('sender', HiddenType::class, [
                "empty_data" => $options['user']
            ])
            ->add('title', TextType::class, [
                'attr' => ["class" => "form-control"]
            ])
            ->add('content', CKEditorType::class)
            ->add('reciever', EntityType::class, [
                "class" => User::class,
                "choice_label" => "nickname",
                'attr' => ["class" => "form-control"],
                'query_builder' => function(UserRepository $userRepository) use($id) {
                    return $userRepository->findFollowers($id);
                }
            ])
            ->add('submit', SubmitType::class, [
                "attr" => ["class" => "btn btn-primary"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);

        $resolver->setRequired([
            'user',
            'id'
        ]);
    }
}

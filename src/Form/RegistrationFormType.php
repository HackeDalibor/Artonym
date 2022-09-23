<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class)
            ->add('email', EmailType::class)
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                // 'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*/',
                        // https://stackoverflow.com/questions/8141125/regex-for-password-php
                        // From the fine folks over at Zorched(https://www.zorched.net/2009/05/08/password-strength-validation-with-regular-expressions/):

                        // /: anchored to beginning and ending of string
                        // \S*: any set of characters
                        // (?=\S{8,}): of at least length 8
                        // (?=\S*[a-z]): containing at least one lowercase letter
                        // (?=\S*[A-Z]): and at least one uppercase letter
                        // (?=\S*[\d]): and at least one number
                        // To include special characters, just add (?=\S*[\W]), which is non-word characters.
                        'message' => 'For your security your password needs to have at least 8 characters with at least one uppercase letter and at least one number'
                    ])
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Your passwords must be the same',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                
            ])
            ->add('captcha', ReCaptchaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // on garde juste ce que l'utilisateur a le droit de voir
        $builder
            ->add('email', EmailType::class, ['label' => false, 'attr' => ['placeholder' => 'entrez votre mail']]) // EmailType ::class est une classe qui permet de matérialisé un champ de type Email
            ->add('password', RepeatedType::class, [
                'constraints' => new Length([
                    'min' => 4,
                    'max' => 30
                ]),
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'first_options' => [
                    'label' => 'Entrez votre mot de passe',
                    'attr' => ['placeholder' => 'entre votre mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => ['placeholder' => 'Confirmez votre mot de passe']
                ],
                'mapped' => false
            ]) // PasswordType::class est une classe qui permet de matérialisé un champ de type password
            // RepeatedType::class est une classe qui permet de matérialisé un champ de type password
            ->add('lastname', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Nom']]) // TextType:class est une classe qui permet de materialisé un champ de type text
            ->add('firstname', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Prénom']])
            ->add('avatar', TextType::class)
            ->add('Inscription', SubmitType::class, ['attr' => ['class' => 'btn btn-dark']]); // attr permet de rajouter des attribut à un champ ex : class, placeholder, ect...
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
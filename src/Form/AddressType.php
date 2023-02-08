<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom voulez-vous donner à cette adresse ?',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre nom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre prénom',
            ])
            ->add('company', TextType::class, [
                'label' => 'Votre société (facultatif)',
                'help' => 'Votre société pour les adresses dans le cadre de votre activité professionnelle',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre adresse',
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'preferred_choices' => ['FR'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre numéro de téléphone',
                'help' => 'Votre numéro de téléphone pour les livraisons',
                'constraints' => new Length([
                    // On admet uniquement que des chiffres et non des lettres  (ex: 06 12 34 56 78)

                    'min' => 10,
                    'max' => 10,
                    'exactMessage' => 'Votre numéro de téléphone doit comporter {{ limit }} chiffres',
                ]),

            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'w3-btn w3-block w3-black'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

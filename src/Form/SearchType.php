<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    // Permet de configurer le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'label' => 'Rechercher par nom de produit',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control mt-3',
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Chercher par catégorie',
                'class'=> Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-block w3-black w-100'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string // Permet de ne pas avoir de préfixe dans l'url
    {
        return '';
    }
}
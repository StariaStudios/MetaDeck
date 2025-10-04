<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\DeckType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('player', TextType::class, [
                'label' => 'Player name (optional)',
            ])
            ->add('deckText', TextareaType::class, [
                'label' => 'Deck list (paste here)',
                'attr' => ['rows' => 20, 'placeholder' => "Pokémon: 19\n4 Munkidori TWM 95\n3 Marnie's Impidimp DRI 134\n..."]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'deck_types' => [],
        ]);
    }
}

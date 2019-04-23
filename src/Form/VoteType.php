<?php

namespace App\Form;

use App\Entity\Vote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('content', TextareaType::class)
            ->add('imageFile', VichImageType::class, [
                'required' => true,
                'allow_delete' => false,
                'download_label' => false,
                'image_uri' => false,
                'download_uri' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Choose a file',
                    'onChange' => 'getOutputFileVote()']]) 
            ->add('duration', ChoiceType::class, ['mapped' => false,
            'choices'  => [
                '1 hour' => 1,
                '2 hours' => 2,
                '4 hours' => 4,
                '8 hours' => 8,
                '24 hours' => 24,
                '48 hours' => 48
            ],
            'empty_data' => '1 hour']) 
            ->add('coord', HiddenType::class, ['mapped' => false, 'data' => ''])
            ->add('save', SubmitType::class, ['label' => 'Submit story'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vote::class,
        ]);
    }
}

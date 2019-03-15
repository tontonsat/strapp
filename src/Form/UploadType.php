<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('imageFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => false,
            'download_label' => false,
            'image_uri' => false,
            'download_uri' => true,
            'label' => false,
            'attr' => ['placeholder' => 'Choose a file',
                    'onChange' => 'getOutputFile()']])         
            ->add('save', SubmitType::class, ['label' => 'Upload']);
    }
}

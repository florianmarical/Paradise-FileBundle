<?php

namespace Paradise\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Paradise\FileBundle\Form\CustomFileType;

class FileUploadType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('file', CustomFileType::class, array(
            'label' => false,
            'path' => $options['path'],
            'filter' => $options['filter'],
            'h' => $options['h'],
            'w' => $options['w'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Paradise\FileBundle\Entity\File',
            'csrf_protection' => false,
            'path' => false,
            'filter' => 'filebundle',
            'h' => null,
            'w' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'paradise_filebundle_file';
    }

}

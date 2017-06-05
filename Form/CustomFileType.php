<?php

namespace Paradise\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CustomFileType extends AbstractType {

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
            'w' => null,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        if (isset($options['path']) && $options['path'] == true) {
            $parentData = $form->getParent()->getData();

            $imageUrl = null;
            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $imageUrl = $accessor->getValue($parentData, 'path');
            }

            // set an "image_url" variable that will be available when rendering this field
            $view->vars['path'] = $imageUrl;
        }
        
        if($options['h'] !== null && $options['w'] !== null){
            $view->vars['h'] = $options['h'];
            $view->vars['w'] = $options['w'];
        }
    }

    public function getParent() {
        return FileType::class;
    }

    public function getBlockPrefix() {
        return 'customfile';
    }

}

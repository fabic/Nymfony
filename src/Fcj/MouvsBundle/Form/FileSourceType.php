<?php

namespace Fcj\MouvsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileSourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', 'text')
            ->add('name', 'text')
            //->add('remote')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fcj\MouvsBundle\Entity\FileSource'
        ));
    }

    public function getName()
    {
        return 'fcj_mouvsbundle_filesourcetype';
    }
}

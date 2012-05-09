<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class OperationGroupForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('required' => true))
                ->add('descr', 'text', array('required' => true))
                ->add('operations', 'entity', array(
                    'multiple' => true,
                    'class' => 'BoilrBundle:Operation',
                    'property' => 'name',
                    'expanded' => true
                ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\OperationGroup'
        );
    }

    public function getName()
    {
        return 'operationGroupForm';
    }

}
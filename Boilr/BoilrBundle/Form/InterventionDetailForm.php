<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;


class InterventionDetailForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
               ->add('operationGroup', 'entity', array(
                                'required' => false,
                                'class'    => 'BoilrBundle:OperationGroup',
                                'property' => 'name',
                                'empty_value' => false,
                     ))
                ->add('checked', 'checkbox', array(
                                'required' => false,
                ))
                ;
    }

    public function getName()
    {
        return 'manteinanceInterventionForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Boilr\BoilrBundle\Entity\InterventionDetail');
    }
}

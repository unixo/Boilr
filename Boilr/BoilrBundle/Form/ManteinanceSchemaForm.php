<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class ManteinanceSchemaForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('systemType', 'entity', array(
                    'class' => 'BoilrBundle:SystemType',
                    'property' => 'name',
                    'empty_value' => ''))
                ->add('isPeriodic', 'checkbox', array('required' => false))
                ->add('freq')
                ->add('operationGroup', 'entity', array(
                    'class' => 'BoilrBundle:OperationGroup',
                    'property' => 'name',
                    'empty_value' => ''))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\ManteinanceSchema'
        );
    }

    public function getName()
    {
        return 'manteinanceSchemaForm';
    }

}
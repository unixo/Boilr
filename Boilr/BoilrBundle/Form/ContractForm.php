<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class ContractForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('startDate', 'date', array(
                                     'required' => true,
                                     'format' => 'dd/MM/yyyy',
                                     'widget' => 'single_text'))
            ->add('endDate', 'date', array(
                                     'required' => true,
                                     'format' => 'dd/MM/yyyy',
                                     'widget' => 'single_text'))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Contract'
        );
    }

    public function getName()
    {
        return 'contractForm';
    }
}

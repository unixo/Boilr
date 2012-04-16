<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of OperationForm
 *
 * @author unixo
 */
class OperationForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Descrizione', 'required' => true))
            ->add('timeLength', 'integer', array('label' => 'Durata stimata (sec)', 'required' => true))
                ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Operation'
        );
    }

    public function getName()
    {
        return 'operationForm';
    }
}
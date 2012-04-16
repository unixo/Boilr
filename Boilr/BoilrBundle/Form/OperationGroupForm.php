<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of OperationGroupForm
 *
 * @author unixo
 */
class OperationGroupForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Sigla', 'required' => true))
            ->add('descr', 'text', array('label' => 'Descrizione', 'required' => true))
                ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\OperationGroup'
        );
    }

    public function getName()
    {
        return 'operationGroupForm';
    }
}
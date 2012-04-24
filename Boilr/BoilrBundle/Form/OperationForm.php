<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;
use Boilr\BoilrBundle\Entity\Operation;

/**
 * Description of OperationForm
 *
 * @author unixo
 */
class OperationForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $resultChoices = array(
            Operation::RESULT_CHECKBOX => 'Risposta multipla',
            Operation::RESULT_NOTE => 'Campo note'
        );

        $builder
                ->add('name', 'text', array(
                    'label' => 'Descrizione',
                    'required' => true))
                ->add('timeLength', 'integer', array(
                    'label' => 'Durata stimata (sec)',
                    'required' => true))
                ->add('resultType', 'choice', array(
                    'label' => 'Tipo risultato',
                    'required' => true, 'choices' => $resultChoices,
                    'preferred_choices' => array(Operation::RESULT_CHECKBOX)))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Operation'
        );
    }

    public function getName()
    {
        return 'operationForm';
    }

}
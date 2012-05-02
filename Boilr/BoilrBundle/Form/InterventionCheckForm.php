<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;
use Boilr\BoilrBundle\Entity\Operation as MyOperation;

/**
 * Description of InterventionCheckForm
 *
 * @author unixo
 */
class InterventionCheckForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('threewayValue', 'choice', array(
                    'label' => 'Risultato',
                    'required' => true,
                    'choices' => array(1 => 'Si', 0 => 'No', 2 => 'N.C.'),
                    'expanded' => true,
                    'multiple' => false,
                    'widget_type'  => "inline",
                ))
                ->add('textValue', 'text', array(
                    'label' => 'Risultato',
                    'required' => true,
                ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\InterventionCheck'
        );
    }

    public function getName()
    {
        return 'interventionCheckForm';
    }

}

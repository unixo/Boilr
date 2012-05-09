<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;


class MaintenanceInterventionForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('details', 'collection', array(
                                'type' => new InterventionDetailForm()
                     ))
                ->add('scheduledDate', 'datetime', array(
                                'required'    => true,
                                'date_format' => 'dd/MM/yyyy',
                                'date_widget' => 'single_text'
                     ))
                ;
    }

    public function getName()
    {
        return 'manteinanceInterventionForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Boilr\BoilrBundle\Entity\MaintenanceIntervention');
    }
}

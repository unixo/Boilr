<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;
use Boilr\BoilrBundle\Entity\MaintenanceIntervention;

class MaintenanceInterventionSearchForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $allStatus = array(
            MaintenanceIntervention::STATUS_ABORTED => 'Annullata',
            MaintenanceIntervention::STATUS_CLOSED => 'Conclusa',
            MaintenanceIntervention::STATUS_CONFIRMED => 'Confermato',
            MaintenanceIntervention::STATUS_SUSPENDED => 'Sospeso',
            MaintenanceIntervention::STATUS_TENTATIVE => 'Da confermare'
        );

        $builder->add('searchByDate', 'checkbox', array('required' => false, 'label' => 'Ricerca per data'))
                ->add('startDate', 'date', array(
                    'label' => 'Inizio intervallo',
                    'required' => false,
                    'format' => 'dd/MM/yyyy',
                    'widget' => 'single_text'
                ))
                ->add('endDate', 'date', array(
                    'label' => 'Fine intervallo',
                    'required' => false,
                    'format' => 'dd/MM/yyyy',
                    'widget' => 'single_text',
                ))
                ->add('planned', 'checkbox', array('required' => false, 'label' => 'Programmato'))
                ->add('withoutInstaller', 'checkbox', array('required' => false))
                ->add('status', 'choice', array(
                    'label' => 'Stato',
                    'choices' => $allStatus,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                ))
        ;
    }

    public function getName()
    {
        return 'interventionSearchForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Form\Model\MaintenanceInterventionFilter'
        );
    }

}

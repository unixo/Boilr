<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;
use Boilr\BoilrBundle\Entity\ManteinanceIntervention;

class ManteinanceInterventionSearchForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $allStatus = array(
            ManteinanceIntervention::STATUS_ABORTED => 'Annullata',
            ManteinanceIntervention::STATUS_CLOSED => 'Conclusa',
            ManteinanceIntervention::STATUS_CONFIRMED => 'Confermato',
            ManteinanceIntervention::STATUS_SUSPENDED => 'Sospeso',
            ManteinanceIntervention::STATUS_TENTATIVE => 'Da confermare'
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
            'data_class' => 'Boilr\BoilrBundle\Form\Model\ManteinanceInterventionFilter'
        );
    }

}

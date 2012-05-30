<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class InstallerForInterventionForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('installer', 'entity', array(
                    'required' => true,
                    'class' => 'BoilrBundle:Installer',
                    'property' => 'fullName',
                    ))
                ->add('checked', 'checkbox', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'installerForInterventionForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Boilr\BoilrBundle\Form\Model\InstallerForIntervention');
    }
}
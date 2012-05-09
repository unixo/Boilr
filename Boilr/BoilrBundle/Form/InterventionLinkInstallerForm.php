<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class InterventionLinkInstallerForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $interv = $options['data'];
        /* @var $interv \Boilr\BoilrBundle\Entity\MaintenanceIntervention */
        $systemType = null;
        if ($interv->getDetails()->count() == 1) {
            $details = $interv->getDetails();
            $systemType = $details[0]->getSystem()->getSystemType();
        } else {
            $details = $interv->getDetails();
        }

        $choices = array();
        foreach ($systemType->getInstallers() as $inst) {
            $choices[$inst->getId()] = $inst;
        }

        $builder
                ->add('installer', 'entity', array(
                    'required' => true,
                    'class' => 'Boilr\BoilrBundle\Entity\Installer',
                    'property' => 'fullName',
                    'choices' => $choices,
                ));
    }

    public function getName()
    {
        return 'manteinanceLinkInstallerForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\MaintenanceIntervention'
        );
    }

}

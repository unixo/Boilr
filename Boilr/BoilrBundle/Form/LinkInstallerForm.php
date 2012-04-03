<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;


class LinkInstallerForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        switch ($options['flowStep']) {
            case 1:
                $builder->add('installer', 'genemu_jqueryautocompleter', array(
                                'route_name' => 'ajax_pick_installer',
                                'class'      => 'Boilr\BoilrBundle\Entity\Person',
                                'widget'     => 'entity',
                             ));
                break;

            case 2:
                break;
        }
    }

    public function getName()
    {
        return 'interventionInstallerForm';
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $options['data_class'] = 'Boilr\BoilrBundle\Entity\ManteinanceIntervention';
        $options['flowStep']   = 1;

        return $options;
    }
}

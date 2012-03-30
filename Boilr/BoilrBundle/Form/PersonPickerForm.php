<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;


class PersonPickerForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'genemu_jqueryautocompleter', array(
            'route_name' => 'ajax_pick_installer',
            'class' => 'Boilr\BoilrBundle\Entity\Person',
            'widget' => 'entity',
        ));
    }
    
    public function getName()
    {
        return 'interventionInstallerForm';
    }
}

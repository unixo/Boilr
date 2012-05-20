<?php

namespace Boilr\BoilrBundle\Form;

use Boilr\BoilrBundle\Entity\Person as MyPerson;
use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class PersonForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('type', 'choice', array(
                    'choices' => array(
                        MyPerson::TYPE_PHYSICAL => 'Persona fisica',
                        MyPerson::TYPE_GIURIDICAL => 'Figura giuridica',
                        MyPerson::TYPE_BUILDING => 'Condominio'),
                    'empty_value' => ''))
                ->add('homePhone', 'text', array('required' => false))
                ->add('officePhone', 'text', array('required' => false))
                ->add('faxNumber', 'text', array('required' => false))
                ->add('cellularPhone', 'text', array('required' => false))
                ->add('primaryMail', 'email', array('required' => false, 'widget_addon' => array('text' => '@')))
                ->add('secondaryMail', 'email', array('required' => false, 'widget_addon' => array('text' => '@')))
                ->add('title', 'text', array('required' => false))
                ->add('name', 'text')
                ->add('surname', 'text')
                ->add('fiscalCode', 'text', array('required' => false))
                ->add('vatCode', 'text', array('required' => false))
                ->add('addresses', 'collection', array(
                    'show_legend' => false,
                    'type' => new AddressForm(),
                    'allow_add' => true, 'allow_delete' => true,
                    'prototype' => true, 'by_reference' => false,
                    'widget_add_btn' => "Aggiungi",
                    'options' => array('widget_remove_btn' => "elimina")
                ));
    }

    public function getName()
    {
        return "personForm";
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Person'
        );
    }

}

<?php

namespace Boilr\BoilrBundle\Form;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\AddressType;
use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class NewPersonForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        switch ($options['flowStep']) {
            case 1:
                $builder->add('type', 'choice', array(
                            'label' => 'Tipo contatto',
                            'choices' => array(
                                MyPerson::TYPE_PHYSICAL => 'Persona fisica',
                                MyPerson::TYPE_GIURIDICAL => 'Figura giuridica',
                                MyPerson::TYPE_BUILDING => 'Condominio'),
                            'empty_value' => ''))
                        ->add('homePhone', 'text', array('label' => 'Telefono casa', 'required' => false))
                        ->add('officePhone', 'text', array('label' => 'Ufficio', 'required' => false))
                        ->add('faxNumber', 'text', array('label' => 'FAX', 'required' => false))
                        ->add('cellularPhone', 'text', array('label' => 'Cellulare', 'required' => false))
                        ->add('primaryMail', 'email', array('label' => 'Email #1', 'required' => false, 'widget_addon' => array('text' => '@')))
                        ->add('secondaryMail', 'email', array('label' => 'Email #2', 'required' => false, 'widget_addon' => array('text' => '@')))
                        ->add('title', 'text', array('label' => 'Titolo', 'required' => false))
                        ->add('name', 'text', array('label' => 'Nome'))
                        ->add('surname', 'text', array('label' => 'Cognome'))
                        ->add('fiscalCode', 'text', array('label' => 'Codice Fiscale', 'required' => false))
                        ->add('vatCode', 'text', array('label' => 'Partita IVA', 'required' => false));
                break;

            case 2:
                $builder->add('addresses', 'collection', array(
                    'type' => new AddressForm(),
                    'allow_add' => true, 'allow_delete' => true,
                    'prototype' => true, 'by_reference' => false
                ));
                break;

            case 3:
                $builder->add('systems', 'collection', array(
                    'type' => new SystemForm(),
                    'allow_add' => true, 'allow_delete' => true,
                    'prototype' => true, 'by_reference' => false
                ));
                // }
                break;

            case 4:
                break;
        }
    }

    public function getName()
    {
        return "newPerson";
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $options['data_class'] = 'Boilr\BoilrBundle\Entity\Person';
        $options['flowStep'] = 1;

        return $options;
    }

}

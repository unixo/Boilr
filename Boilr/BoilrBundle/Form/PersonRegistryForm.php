<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Boilr\BoilrBundle\Entity\Person as MyPerson;


class PersonRegistryForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('type', 'choice', array(
                        'label' => 'Tipo contatto',
                        'choices' => array(
                            MyPerson::TYPE_PHYSICAL   => 'Persona fisica',
                            MyPerson::TYPE_GIURIDICAL => 'Figura giuridica',
                            MyPerson::TYPE_BUILDING   => 'Condominio'),
                        'empty_value' => ''));

        $builder->add('isCustomer',      'checkbox', array('label' => 'Cliente?',        'required' => false))
                ->add('isInstaller',     'checkbox', array('label' => 'Installatore?',   'required' => false))
                ->add('isAdministrator', 'checkbox', array('label' => 'Amministratore?', 'required' => false));

        $builder
            ->add('homePhone',     'text',     array('label' => 'Telefono casa', 'required' => false))
            ->add('officePhone',   'text',     array('label' => 'Ufficio',       'required' => false))
            ->add('faxNumber',     'text',     array('label' => 'FAX',           'required' => false))
            ->add('cellularPhone', 'text',     array('label' => 'Cellulare',     'required' => false))
            ->add('primaryMail',   'email',    array('label' => 'Email #1',      'required' => false))
            ->add('secondaryMail', 'email',    array('label' => 'Email #2',      'required' => false))
            ->add('title',         'text',     array('label' => 'Titolo',        'required' => false))
            ->add('name',          'text',     array('label' => 'Nome'))
            ->add('surname',       'text',     array('label' => 'Cognome'))
            ->add('fiscalCode',    'text',     array('label' => 'Codice Fiscale', 'required' => false))
            ->add('vatCode',       'text',     array('label' => 'Partita IVA',    'required' => false));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Person'
        );
    }

    public function getName()
    {
        return 'personRegistryForm';
    }
}

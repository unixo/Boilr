<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class InstallerForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('company', 'entity', array('label' => 'Società',
                    'required' => true, 'class' => 'BoilrBundle:Company',
                    'property' => 'name'))
                ->add('officePhone', 'text', array('label' => 'Ufficio',
                    'required' => false))
                ->add('name', 'text', array('label' => 'Nome'))
                ->add('surname', 'text', array('label' => 'Cognome'))
                ->add('vatCode', 'text', array('label' => 'Partita IVA',
                    'required' => false))
                ->add('abilities', 'entity', array(
                    'label' => 'Abilità', 'multiple' => true,
                    'expanded' => true, 'property' => 'name',
                    'class' => 'BoilrBundle:SystemType'))
        ;
    }

    public function getName()
    {
        return 'installerForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'Boilr\BoilrBundle\Entity\Installer');
    }

}

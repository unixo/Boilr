<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class SystemForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $system = $options['data'];
        /* @var $system \Boilr\BoilrBundle\Entity\System */

        $addresses = array();
        if ($system) {
            $addresses = $system->getOwner()->getAddresses()->getValues();
        }

        $builder->add('systemType', 'entity', array(
                                     'label'       => 'Tipo',
                                     'class'       => 'BoilrBundle:SystemType',
                                     'property'    => 'name',
                                     'empty_value' => ''))
                ->add('product', 'entity', array(
                                     'label'       => 'Prodotto',
                                     'class'       => 'BoilrBundle:Product',
                                     'property'    => 'name',
                                     'empty_value' => ''))
                ->add('address', 'entity', array(
                                     'label'       => 'Indirizzo',
                                     'class'       => 'BoilrBundle:Address',
                                     'property'    => 'address',
                                     'choices'     => $addresses,
                                     'empty_value' => ''
                     ))
                ->add('installDate', 'date', array(
                                     'label'       => 'Data installazione',
                                     'required'    => true,
                                     'format'      => 'dd/MM/yyyy',
                                     'widget'      => 'single_text'))
                ->add('lastManteinance', 'date', array(
                                     'label'       => 'Ultima manutenzione',
                                     'format'      => 'dd/MM/yyyy',
                                     'widget'      => 'single_text'))
                ->add('code',  'text', array('required' => true, 'label' => 'Seriale'))
                ->add('descr', 'text', array('required' => true, 'label' => 'Descrizione'));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\System'
        );
    }

    public function getName()
    {
        return 'systemForm';
    }
}

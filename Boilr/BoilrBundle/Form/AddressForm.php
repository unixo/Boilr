<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Boilr\BoilrBundle\Entity\Address as MyAddress;

class AddressForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('type', 'choice', array(
                              'label' => 'Tipo',
                              'choices' => array(
                                    MyAddress::TYPE_HOME   => 'Casa',
                                    MyAddress::TYPE_OFFICE => 'Ufficio',
                                    MyAddress::TYPE_OTHER  => 'Altro'),
                              'empty_value' => ''))
                ->add('street',     'text', array('required' => true, 'label' => 'Indirizzo'))
                ->add('postalCode', 'text', array('required' => true, 'label' => 'CAP'))
                ->add('city',       'text', array('required' => true, 'label' => 'Città'))
                ->add('state',      'country', array('required' => true, 'label' => 'Stato'))
                ->add('province',   'province', array('required' => true, 'label' => 'Provincia'));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Address'
        );
    }

    public function getName()
    {
        return 'boilr_boilrbundle_addressform';
    }
}

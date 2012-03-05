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
                              'choices' => array(
                                    MyAddress::TYPE_HOME   => 'Casa',
                                    MyAddress::TYPE_OFFICE => 'Ufficio',
                                    MyAddress::TYPE_OTHER  => 'Altro'),
                              'empty_value' => ''))
                ->add('street',     'text', array('required' => true))
                ->add('postalCode', 'text', array('required' => true))
                ->add('city',       'text', array('required' => true))
                ->add('state',      'text', array('required' => true))
                ->add('province',   'province', array('required' => true));
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

<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Boilr\BoilrBundle\Entity\Address as MyAddress;

class SystemForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('systemType', 'entity', array(
                                    'class'       => 'BoilrBundle:SystemType',
                                    'property'    => 'name',
                                    'empty_value' => ''))
                ->add('product', 'entity', array(
                                     'class'       => 'BoilrBundle:Product',
                                     'property'    => 'name',
                                     'empty_value' => ''))
                ->add('installDate', 'date', array(
                                     'required' => true,
                                     'format' => 'dd-MM-yyyy',
                                     'widget' => 'single_text'))
                ->add('lastManteinance', 'date', array(
                                     'format' => 'dd-MM-yyyy',
                                     'widget' => 'single_text'))
                ->add('code',  'text', array('required' => true))
                ->add('descr', 'text', array('required' => true));
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\System'
        );
    }

    public function getName()
    {
        return 'boilr_boilrbundle_systemform';
    }
}

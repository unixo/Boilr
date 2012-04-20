<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CompanyForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('required' => true, 'label' => 'Intestazione'))
                ->add('vatCode', 'text', array('required' => false, 'label' => 'Partita IVA'))
                ->add('street', 'text', array('required' => true, 'label' => 'Indirizzo'))
                ->add('postalCode', 'text', array('required' => true, 'label' => 'CAP'))
                ->add('city', 'text', array('required' => true, 'label' => 'Città'))
                ->add('state', 'country', array('required' => true, 'label' => 'Stato'))
                ->add('province', 'province', array('required' => true, 'label' => 'Provincia'));
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Company'
        );
    }

    public function getName()
    {
        return 'companyForm';
    }

}

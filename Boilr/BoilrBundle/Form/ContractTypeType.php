<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ContractTypeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('periodicity')
        ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\ContractType'
        );
    }
    
    public function getName()
    {
        return 'boilr_boilrbundle_contracttypetype';
    }
}

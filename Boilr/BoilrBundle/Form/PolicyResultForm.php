<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class PolicyResultForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('associations', 'collection', array(
                    'type' => new InstallerForInterventionForm(),
                    'show_legend' => false,
                    'allow_add' => false, 'allow_delete' => false,
                    'prototype' => false, 'by_reference' => false,
                ))
        ;
    }

    public function getName()
    {
        return 'associationForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Policy\PolicyResult'
        );
    }

}
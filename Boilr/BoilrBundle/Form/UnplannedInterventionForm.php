<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Doctrine\ORM\EntityRepository;

use Boilr\BoilrBundle\Entity\Address as MyAddress;

class UnplannedInterventionForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $intervention = $options['data'];
        /* @var $intervention \Boilr\BoilrBundle\Entity\ManteinanceIntervention */

        $builder->add('system',       'entity', array(
                                        'class'       => 'BoilrBundle:System',
                                        'property'    => 'descr',
                                        'choices'     => $intervention->getCustomer()->getSystems()->getValues(),
                                        'empty_value' => '')
                     )
                ->add('address', 'entity', array(
                                        'class'       => 'BoilrBundle:Address',
                                        'property'    => 'address',
                                        'choices'     => $intervention->getCustomer()->getAddresses()->getValues(),
                                        'empty_value' => '')
                     )
                ->add('originalDate', 'datetime', array(
                                                'required' => true,
                                                //'format'   => 'dd/MM/yyyy',
                                                //'date_widget'   => 'single_text')
                                                )
                     )
                ->add('defaultOperationGroup', 'entity', array(
                                    'class' => 'BoilrBundle:OperationGroup',
                                    'property' => 'name'
                     ));

    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class'  => 'Boilr\BoilrBundle\Entity\ManteinanceIntervention',
        );
    }

    public function getName()
    {
        return 'unplannedInterventionForm';
    }
}

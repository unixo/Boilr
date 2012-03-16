<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Doctrine\ORM\EntityRepository;

use Boilr\BoilrBundle\Entity\Address as MyAddress;

class UnplannedInterventionForm extends AbstractType
{
    /**
     * @var EntityRepository
     */
    protected $systemRepository;

    function __construct(EntityRepository $systemRep)
    {
       $this->systemRepository = $systemRep;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $intervention = $options['data'];
        /* @var $intervention \Boilr\BoilrBundle\Entity\ManteinanceIntervention */
        $owner = $intervention->getCustomer();

        $qb = $this->systemRepository->createQueryBuilder('s')
                                     ->where('s.owner = :owner')

                                     ->setParameter('owner', $owner);


        $builder->add('system',       'entity', array(
                                                'class'         => 'BoilrBundle:System',
                                                'property'      => 'descr',
                                                'query_builder' => $qb,
                                                'empty_value'   => '')
                     )
                ->add('originalDate', 'date', array(
                                                'required' => true,
                                                'format'   => 'dd/MM/yyyy',
                                                'widget'   => 'single_text')
                     )
                ->add('defaultOperationGroup', 'entity', array(
                                    'class' => 'BoilrBundle:OperationGroup',
                                    'property' => 'name'
                    ));

    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\ManteinanceIntervention'
        );
    }

    public function getName()
    {
        return 'unplannedInterventionForm';
    }
}

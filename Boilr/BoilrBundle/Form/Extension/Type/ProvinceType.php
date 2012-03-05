<?php

namespace Boilr\BoilrBundle\Form\Extension\Type;

use Symfony\Component\Form\AbstractType;
use Boilr\BoilrBundle\Form\Extension\ChoiceList\ProvinceChoiceList;

class ProvinceType extends AbstractType
{
    protected $format;
	
    public function __construct($format = "%k - %v") 
    {
	$this->format = $format;
    }
	
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'choice_list' => new ProvinceChoiceList($this->format),
            'empty_value' => ''
                );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'province';
    }
}

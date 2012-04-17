<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of TemplateSectionBindOp
 *
 * @author unixo
 */
class TemplateSectionOpForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('operations', 'entity', array(
                                            'required' => true,
                                            'multiple' => true,
                                            'class'    => 'BoilrBundle:Operation',
                                            'property' => 'name',
                                            'expanded' => true
                ))
                ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\TemplateSection'
        );
    }

    public function getName()
    {
        return 'templateSectionForm';
    }
}
<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of TemplateSectionForm
 *
 * @author unixo
 */
class TemplateSectionForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('required' => true))
                ->add('timeLength', 'integer', array('required' => true))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\TemplateSection'
        );
    }

    public function getName()
    {
        return 'templateSectionForm';
    }

}
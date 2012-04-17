<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Boilr\BoilrBundle\Form\TemplateSectionForm;

/**
 * Description of TemplateForm
 *
 * @author unixo
 */
class TemplateForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'Nome', 'required' => true))
            ->add('descr', 'text', array('label' => 'Descrizione', 'required' => true))
                ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Template'
        );
    }

    public function getName()
    {
        return 'templateForm';
    }
}
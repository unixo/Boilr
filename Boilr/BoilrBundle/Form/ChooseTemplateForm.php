<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of ChooseTemplateForm
 *
 * @author unixo
 */
class ChooseTemplateForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('template', 'entity', array(
                    'label' => 'Nome modello', 'required' => true,
                    'class' => 'BoilrBundle:Template', 'property' => 'name',
                    ))
        ;
    }

    public function getName()
    {
        return 'chooseTemplateForm';
    }
}

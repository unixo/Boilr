<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;
use Boilr\BoilrBundle\Form\InterventionCheckForm;

/**
 * Description of DetailResultsForm
 *
 * @author unixo
 */
class DetailResultsForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('checks', 'collection', array(
                'type' => new InterventionCheckForm()
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Form\Model\InterventionDetailResults'
        );
    }

    public function getName()
    {
        return 'detailResultsForm';
    }
}

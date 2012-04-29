<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

/**
 * Description of AttachmentForm
 *
 * @author unixo
 */
class AttachmentForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('label' => 'Titolo documento', 'required' => true))
                ->add('document', 'file', array('label' => 'Nome del file', 'required' => true))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\Attachment'
        );
    }

    public function getName()
    {
        return 'attachmentForm';
    }

}

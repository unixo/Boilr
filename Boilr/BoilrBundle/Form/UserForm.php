<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

use Boilr\BoilrBundle\Listener\UserFormSubscriber;

/**
 * Description of UserForm
 *
 * @author unixo
 */
class UserForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $subscriber = new UserFormSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);

        $builder
            ->add('surname', 'text',      array('required' => true, 'label' => 'Cognome'))
            ->add('name', 'text',         array('required' => true, 'label' => 'Nome'))
            ->add('password', 'repeated', array('type' => 'password', 'label' => 'Password', 'required' => false))
            ->add('isActive', 'checkbox', array('required' => true, 'label' => 'Attivo'))
            ->add('groups', 'entity',     array(
                                            'required' => true,
                                            'multiple' => true,
                                            'class'    => 'BoilrBundle:Group',
                                            'property' => 'name',
                                            'expanded' => true
                ))
                ;
    }

    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\User'
        );
    }

    public function getName()
    {
        return 'userForm';
    }
}
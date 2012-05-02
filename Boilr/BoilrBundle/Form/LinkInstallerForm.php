<?php

namespace Boilr\BoilrBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder;

class LinkInstallerForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $system = $options['data'];
        /* @var $system \Boilr\BoilrBundle\Entity\System */
        $installers = $system->getSystemType()->getInstallers();
        $choices = array();
        foreach ($installers as $inst) {
            $choices[$inst->getId()] = $inst;
        }

        $builder
                ->add('defaultInstaller', 'entity', array(
                    'required' => true,
                    'class' => 'Boilr\BoilrBundle\Entity\Installer',
                    'property' => 'fullName',
                    'choices' => $choices,
                ));
    }

    public function getName()
    {
        return 'linkInstallerForm';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Boilr\BoilrBundle\Entity\System'
        );
    }

}

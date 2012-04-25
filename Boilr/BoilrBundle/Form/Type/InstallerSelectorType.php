<?php

namespace Boilr\BoilrBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Doctrine\Common\Persistence\ObjectManager;

use Boilr\BoilrBundle\Form\DataTransformer\InstallerToNumberTransformer;

/**
 * Description of InstallerSelectorType
 *
 * @author unixo
 */
class InstallerSelectorType extends AbstractType
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $transformer = new InstallerToNumberTransformer($this->om);
        $builder->prependClientTransformer($transformer);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'invalid_message' => 'The selected installer does not exist',
            /*
            'class' => 'BoilrBundle:Installer',
            'property' => 'fullName'
             */
        );
    }

    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getName()
    {
        return 'installer_selector';
    }
}

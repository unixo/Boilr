<?php

namespace Boilr\BoilrBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException,
    Symfony\Component\Form\DataTransformerInterface,
    Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of InstallerToNumberTransformer
 *
 * @author unixo
 */
class InstallerToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an installer object to a string.
     *
     * @param  \Boilr\BoilrBundle\Entity\Installer|null $installer
     * @return string
     */
    public function transform($installer)
    {
        if (null === $installer || '' === $installer) {
            return "";
        }

        return $installer->getId();
    }

    /**
     * Transforms a string to an installer object.
     *
     * @param  string $number
     * @return \Boilr\BoilrBundle\Entity\Installer|null
     *
     * @throws TransformationFailedException if installer object is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $installer = $this->om->getRepository('BoilrBundle:Installer')
                              ->findOneBy(array('id' => $number));

        if (null === $installer) {
            throw new TransformationFailedException(sprintf(
                'An installer with number "%s" does not exist!',
                $number
            ));
        }

        return $installer;
    }
}

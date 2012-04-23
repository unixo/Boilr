<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\SystemType
 *
 * @ORM\Table(name="system_types")
 * @ORM\Entity
 */
class SystemType
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var Installer
     *
     * @ORM\ManyToMany(targetEntity="Installer", mappedBy="abilities")
     */
    protected $installers;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->installers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add installers
     *
     * @param Boilr\BoilrBundle\Entity\Installer $installers
     */
    public function addInstaller(\Boilr\BoilrBundle\Entity\Installer $installers)
    {
        $this->installers[] = $installers;
    }

    /**
     * Get installers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getInstallers()
    {
        return $this->installers;
    }
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\Template
 *
 * @ORM\Table(name="templates")
 * @ORM\Entity
 */
class Template
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string $descr
     *
     * @ORM\Column(name="descr", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $descr;

    /**
     * @var TemplateItem
     *
     * @ORM\OneToMany(targetEntity="TemplateSection", mappedBy="template", cascade={"persist", "remove"})
     * @ORM\OrderBy({"listOrder" = "ASC"})
     */
    protected $sections;

    public function __construct()
    {
        $this->sections = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add sections
     *
     * @param Boilr\BoilrBundle\Entity\TemplateSection $sections
     */
    public function addTemplateSection(\Boilr\BoilrBundle\Entity\TemplateSection $sections)
    {
        $this->sections[] = $sections;
    }

    public function setSections(\Doctrine\ORM\PersistentCollection $sections)
    {
        $this->sections[] = $sections;
    }

    /**
     * Get sections
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    public function getDescr()
    {
        return $this->descr;
    }

    public function setDescr($descr)
    {
        $this->descr = $descr;
    }
}
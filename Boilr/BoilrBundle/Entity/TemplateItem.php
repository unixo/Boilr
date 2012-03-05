<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\TemplateItem
 *
 * @ORM\Table(name="template_items")
 * @ORM\Entity
 */
class TemplateItem
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
     * @var integer $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     */
    protected $listOrder;

    /**
     * @var TemplateSection
     *
     * @ORM\ManyToOne(targetEntity="TemplateSection", inversedBy="items")
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    protected $section;

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
     * Set timeLength
     *
     * @param integer $timeLength
     */
    public function setTimeLength($timeLength)
    {
        $this->timeLength = $timeLength;
    }

    /**
     * Get timeLength
     *
     * @return integer
     */
    public function getTimeLength()
    {
        return $this->timeLength;
    }

    /**
     * Set section
     *
     * @param Boilr\BoilrBundle\Entity\TemplateSection $section
     */
    public function setSection(\Boilr\BoilrBundle\Entity\TemplateSection $section)
    {
        $this->section = $section;
    }

    /**
     * Get section
     *
     * @return Boilr\BoilrBundle\Entity\TemplateSection
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set listOrder
     *
     * @param integer $listOrder
     */
    public function setListOrder($listOrder)
    {
        $this->listOrder = $listOrder;
    }

    /**
     * Get listOrder
     *
     * @return integer 
     */
    public function getListOrder()
    {
        return $this->listOrder;
    }
}
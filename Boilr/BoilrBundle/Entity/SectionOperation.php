<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="operations_per_section")
 */
class SectionOperation
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    protected $listOrder;

    /**
     * @var TemplateSection
     *
     * @ORM\ManyToOne(targetEntity="TemplateSection", inversedBy="operations")
     * @ORM\JoinColumn(name="template_section_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    protected $parentSection;

    /**
     * @var Operation
     *
     * @ORM\ManyToOne(targetEntity="Operation", cascade={"persist"})
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    protected $parentOperation;


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

    /**
     * Set parentSection
     *
     * @param Boilr\BoilrBundle\Entity\TemplateSection $parentSection
     */
    public function setParentSection(\Boilr\BoilrBundle\Entity\TemplateSection $parentSection)
    {
        $this->parentSection = $parentSection;
    }

    /**
     * Get parentSection
     *
     * @return Boilr\BoilrBundle\Entity\TemplateSection
     */
    public function getParentSection()
    {
        return $this->parentSection;
    }

    /**
     * Set parentOperation
     *
     * @param Boilr\BoilrBundle\Entity\Operation $parentOperation
     */
    public function setParentOperation(\Boilr\BoilrBundle\Entity\Operation $parentOperation)
    {
        $this->parentOperation = $parentOperation;
    }

    /**
     * Get parentOperation
     *
     * @return Boilr\BoilrBundle\Entity\Operation
     */
    public function getParentOperation()
    {
        return $this->parentOperation;
    }
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\TemplateSection
 *
 * @ORM\Table(name="template_sections")
 * @ORM\Entity
 */
class TemplateSection
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer $timeLength
     *
     * @ORM\Column(name="time_length", type="integer", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    private $timeLength;

    /**
     * @var integer $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    protected $listOrder;

    /**
     * @var SectionOperation
     *
     * @ORM\OneToMany(targetEntity="SectionOperation", mappedBy="parentSection", cascade={"persist"})
     * @ORM\OrderBy({"listOrder" = "ASC"})
     */
    protected $operations;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="sections")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    protected $template;

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
     * Set template
     *
     * @param Boilr\BoilrBundle\Entity\Template $template
     */
    public function setTemplate(\Boilr\BoilrBundle\Entity\Template $template)
    {
        $this->template = $template;
    }

    /**
     * Get template
     *
     * @return Boilr\BoilrBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
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
     * Add operations
     *
     * @param Boilr\BoilrBundle\Entity\SectionOperation $operations
     */
    public function addSectionOperation(\Boilr\BoilrBundle\Entity\SectionOperation $operation)
    {
        $this->operations[] = $operation;
        $this->timeLength += $operation->getParentOperation()->getTimeLength();
    }

    public function removeSectionOperation(\Boilr\BoilrBundle\Entity\SectionOperation $operation)
    {
        $this->timeLength -= $operation->getParentOperation()->getTimeLength();
        $this->getOperations()->remoteElement($operation);
    }

    public function getOperations()
    {
        return $this->operations;
    }

    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Operation
     *
     * @ORM\ManyToMany(targetEntity="Operation", inversedBy="sections")
     * @ORM\JoinTable(name="operation_sections",
     *      joinColumns={@ORM\JoinColumn(name="section_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="operation_id", referencedColumnName="id")}
     *      )
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
     * @var integer $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    protected $listOrder;

    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add operations
     *
     * @param Boilr\BoilrBundle\Entity\Operation $operations
     */
    public function addOperation(\Boilr\BoilrBundle\Entity\Operation $operations)
    {
        $this->operations[] = $operations;
    }

    /**
     * Get operations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }
}
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
     * @var integer $timeLength
     *
     * @ORM\Column(name="time_length", type="integer", nullable=false)
     */
    private $timeLength;

    /**
     * @var TemplateItem
     *
     * @ORM\OneToMany(targetEntity="TemplateItem", mappedBy="section")
     */
    protected $items;

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
     */
    protected $listOrder;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $group;

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
     * Add items
     *
     * @param Boilr\BoilrBundle\Entity\TemplateItem $items
     */
    public function addTemplateItem(\Boilr\BoilrBundle\Entity\TemplateItem $items)
    {
        $this->items[] = $items;
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set group
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $group
     */
    public function setGroup(\Boilr\BoilrBundle\Entity\OperationGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Get group
     *
     * @return Boilr\BoilrBundle\Entity\OperationGroup
     */
    public function getGroup()
    {
        return $this->group;
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
     * Set order
     *
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
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
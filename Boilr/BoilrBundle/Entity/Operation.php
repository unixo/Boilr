<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

use Boilr\BoilrBundle\Entity\OperationGroup;

/**
 * Boilr\BoilrBundle\Entity\Operation
 *
 * @ORM\Entity
 * @ORM\Table(name="operations")
 */
class Operation
{
    const RESULT_CHECKBOX = 1;
    const RESULT_NOTE = 2;

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var integer $timeLength
     *
     * @ORM\Column(name="time_length", type="integer", nullable=false)
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    private $timeLength;

    /**
     * @var integer $resultType
     *
     * @ORM\Column(name="result_type", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $resultType;

    /**
     * @var integer $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    protected $listOrder;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup", inversedBy="operations")
     * @ORM\JoinColumn(name="op_group_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank
     */
    protected $parentGroup;

    protected $sections;

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
     * Set parentGroup
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $parentGroup
     */
    public function setParentGroup(\Boilr\BoilrBundle\Entity\OperationGroup $parentGroup)
    {
        $this->parentGroup = $parentGroup;
    }

    /**
     * Get parentGroup
     *
     * @return Boilr\BoilrBundle\Entity\OperationGroup
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
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
     * Set resultType
     *
     * @param integer $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    /**
     * Get resultType
     *
     * @return integer
     */
    public function getResultType()
    {
        return $this->resultType;
    }
}
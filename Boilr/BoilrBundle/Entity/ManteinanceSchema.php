<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\ManteinanceSchema
 *
 * @ORM\Table(name="manteinance_schema")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\ManteinanceSchemaRepository")
 */
class ManteinanceSchema
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
     * @var SystemType
     *
     * @ORM\ManyToOne(targetEntity="SystemType")
     * @ORM\JoinColumn(name="systemtype_id", referencedColumnName="id", nullable=false)
     */
    protected $systemType;

    /**
     * @var $listOrder
     *
     * @ORM\Column(name="list_order", type="integer", nullable=false)
     * @Assert\Type(type="integer")
     */
    protected $listOrder;

    /**
     * @var $isPeriodic
     *
     * @ORM\Column(name="is_periodic", type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $isPeriodic;

    /**
     * @var string $freq
     *
     * @ORM\Column(name="freq", type="string", length=15, nullable=false)
     */
    protected $freq;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $operationGroup;

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
     * Set isPeriodic
     *
     * @param boolean $isPeriodic
     */
    public function setIsPeriodic($isPeriodic)
    {
        $this->isPeriodic = $isPeriodic;
    }

    /**
     * Get isPeriodic
     *
     * @return boolean
     */
    public function getIsPeriodic()
    {
        return $this->isPeriodic;
    }

    /**
     * Set freq
     *
     * @param string $freq
     */
    public function setFreq($freq)
    {
        $this->freq = $freq;
    }

    /**
     * Get freq
     *
     * @return string
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * Set manteinanceSchema
     *
     * @param Boilr\BoilrBundle\Entity\ManteinanceSchema $manteinanceSchema
     */
    public function setManteinanceSchema(\Boilr\BoilrBundle\Entity\ManteinanceSchema $manteinanceSchema)
    {
        $this->manteinanceSchema = $manteinanceSchema;
    }

    /**
     * Get manteinanceSchema
     *
     * @return Boilr\BoilrBundle\Entity\ManteinanceSchema
     */
    public function getManteinanceSchema()
    {
        return $this->manteinanceSchema;
    }

    /**
     * Set operationGroup
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $operationGroup
     */
    public function setOperationGroup(\Boilr\BoilrBundle\Entity\OperationGroup $operationGroup)
    {
        $this->operationGroup = $operationGroup;
    }

    /**
     * Get operationGroup
     *
     * @return Boilr\BoilrBundle\Entity\OperationGroup
     */
    public function getOperationGroup()
    {
        return $this->operationGroup;
    }

    /**
     * Set list order
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
     * Set systemType
     *
     * @param Boilr\BoilrBundle\Entity\SystemType $systemType
     */
    public function setSystemType(\Boilr\BoilrBundle\Entity\SystemType $systemType)
    {
        $this->systemType = $systemType;
    }

    /**
     * Get systemType
     *
     * @return Boilr\BoilrBundle\Entity\SystemType
     */
    public function getSystemType()
    {
        return $this->systemType;
    }
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\ManteinanceSchema
 *
 * @ORM\Table(name="manteinance_schema")
 * @ORM\Entity
 */
class ManteinanceSchema
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
     * @var SystemType
     *
     * @ORM\ManyToOne(targetEntity="SystemType")
     * @ORM\JoinColumn(name="systemtype_id", referencedColumnName="id", nullable=false)
     */
    protected $systemType;

    /**
     * @var $order
     *
     * @ORM\Column(name="order_pos", type="integer", nullable=false)
     * @Assert\Type(type="integer")
     */
    protected $order;

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
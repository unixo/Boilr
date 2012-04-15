<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\InterventionCheck
 *
 * @ORM\Table(name="intervention_check")
 * @ORM\Entity
 */
class InterventionCheck
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $parent;

    /**
     * @var InterventionDetail
     *
     * @ORM\ManyToOne(targetEntity="InterventionDetail")
     * @ORM\JoinColumn(name="detail_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    protected $parentDetail;

    /**
     * @var Operation
     *
     * @ORM\ManyToOne(targetEntity="Operation")
     * @ORM\JoinColumn(name="operation_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    protected $parentOperation;

    /**
     * @var string $id
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $value;

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set parentDetail
     *
     * @param Boilr\BoilrBundle\Entity\InterventionDetail $parentDetail
     */
    public function setParentDetail(\Boilr\BoilrBundle\Entity\InterventionDetail $parentDetail)
    {
        $this->parentDetail = $parentDetail;
    }

    /**
     * Get parentDetail
     *
     * @return Boilr\BoilrBundle\Entity\InterventionDetail 
     */
    public function getParentDetail()
    {
        return $this->parentDetail;
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
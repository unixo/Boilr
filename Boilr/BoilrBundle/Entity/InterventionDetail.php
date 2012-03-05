<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\InterventionDetail
 *
 * @ORM\Table(name="intervention_detail", uniqueConstraints={
 * @ORM\UniqueConstraint(name="sys_int_idx", columns={"intervention_id", "system_id"})})
 * @ORM\Entity
 */
class InterventionDetail
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
     * @var ManteinanceIntervention
     *
     * @ORM\ManyToOne(targetEntity="ManteinanceIntervention")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    protected $intervention;

    /**
     * @var System
     *
     * @ORM\ManyToOne(targetEntity="System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=false)
     */
    protected $system;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup")
     * @ORM\JoinColumn(name="op_group_id", referencedColumnName="id", nullable=false)
     */
    protected $operationGroup;

    /**
     * @var InterventionCheck
     *
     * @ORM\OneToMany(targetEntity="InterventionCheck", mappedBy="person", cascade={"persist"})
     */
    protected $checks = array();

    public function __construct()
    {
        $this->checks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set intervention
     *
     * @param Boilr\BoilrBundle\Entity\ManteinanceIntervention $intervention
     */
    public function setIntervention(\Boilr\BoilrBundle\Entity\ManteinanceIntervention $intervention)
    {
        $this->intervention = $intervention;
    }

    /**
     * Get intervention
     *
     * @return Boilr\BoilrBundle\Entity\ManteinanceIntervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    /**
     * Set system
     *
     * @param Boilr\BoilrBundle\Entity\System $system
     */
    public function setSystem(\Boilr\BoilrBundle\Entity\System $system)
    {
        $this->system = $system;
    }

    /**
     * Get system
     *
     * @return Boilr\BoilrBundle\Entity\System
     */
    public function getSystem()
    {
        return $this->system;
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
     * Add checks
     *
     * @param Boilr\BoilrBundle\Entity\InterventionCheck $checks
     */
    public function addInterventionCheck(\Boilr\BoilrBundle\Entity\InterventionCheck $checks)
    {
        $this->checks[] = $checks;
    }

    /**
     * Get checks
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChecks()
    {
        return $this->checks;
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
}
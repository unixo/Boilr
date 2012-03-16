<?php

namespace Boilr\BoilrBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext,
    Doctrine\ORM\Mapping as ORM,
    Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\ManteinanceIntervention
 *
 * @ORM\Table(name="maintenance_intervention")
 * @ORM\Entity
 * @Gedmo\Timestampable
 * @Assert\Callback(methods={"isUnplannedValid"}, groups={"unplanned"})
 */
class ManteinanceIntervention
{
    const STATUS_TENTATIVE = 0;
    const STATUS_OPEN      = 1;
    const STATUS_CLOSED    = 2;
    const STATUS_ABORTED   = 3;
    const STATUS_SUSPENDED = 4;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var isPlanned
     *
     * @ORM\Column(name="is_planned", type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $isPlanned;

    /**
     * @var Contract
     *
     * @ORM\ManyToOne(targetEntity="Contract")
     * @ORM\JoinColumn(name="contract_id", referencedColumnName="id", nullable=true)
     */
    protected $contract;

    /**
     * @var integer $status
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     */
    protected $status;

    /**
     * @var date $originalDate
     *
     * @ORM\Column(name="original_date", type="date", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     * @Assert\Date(groups={"unplanned"})
     */
    protected $originalDate;

    /**
     * @var datetime $closeDate
     *
     * @ORM\Column(name="close_date", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    protected $closeDate;

    /**
     * @var $isConfirmed
     *
     * @ORM\Column(name="is_confirmed", type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $isConfirmed = false;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     */
    protected $customer;

    /**
     * @var System
     *
     * @ORM\ManyToOne(targetEntity="System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     */
    protected $system;

    /**
     * @var Installer
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="installer_id", referencedColumnName="id", nullable=true)
     */
    protected $installer;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup")
     * @ORM\JoinColumn(name="oper_group_id", referencedColumnName="id", nullable=false)
     * * @Assert\NotBlank(groups={"unplanned"})
     */
    protected $defaultOperationGroup;

    /**
     * @var InterventionDetail
     *
     * @ORM\OneToMany(targetEntity="InterventionDetail", mappedBy="intervention")
     * @Assert\Valid
     */
    protected $details;

    public static function UnplannedInterventionFactory()
    {
        $int = new ManteinanceIntervention();
        $int->setIsPlanned(false);
        $int->setStatus(self::STATUS_TENTATIVE);

        return $int;
    }

    public function isUnplannedValid(ExecutionContext $context)
    {
        $now = new \DateTime();
        if ($this->getOriginalDate() <= $now) {
            $property_path = $context->getPropertyPath() . ".originalDate";
            $context->setPropertyPath($property_path);
            $context->addViolation('Non è possibile creare un evento nel passato', array(), null);
        }
    }

    public function __construct()
    {
        $this->details = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set isPlanned
     *
     * @param boolean $isPlanned
     */
    public function setIsPlanned($isPlanned)
    {
        $this->isPlanned = $isPlanned;
    }

    /**
     * Get isPlanned
     *
     * @return boolean
     */
    public function getIsPlanned()
    {
        return $this->isPlanned;
    }

    /**
     * Set closeDate
     *
     * @param datetime $closeDate
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
    }

    /**
     * Get closeDate
     *
     * @return datetime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set customer
     *
     * @param Boilr\BoilrBundle\Entity\Person $customer
     */
    public function setCustomer(\Boilr\BoilrBundle\Entity\Person $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Get customer
     *
     * @return Boilr\BoilrBundle\Entity\Person
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Add details
     *
     * @param Boilr\BoilrBundle\Entity\InterventionDetail $details
     */
    public function addInterventionDetail(\Boilr\BoilrBundle\Entity\InterventionDetail $details)
    {
        $this->details[] = $details;
    }

    /**
     * Get details
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set isConfirmed
     *
     * @param boolean $isConfirmed
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * Get isConfirmed
     *
     * @return boolean
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Set installer
     *
     * @param Boilr\BoilrBundle\Entity\Person $installer
     */
    public function setInstaller(\Boilr\BoilrBundle\Entity\Person $installer)
    {
        $this->installer = $installer;
    }

    /**
     * Get installer
     *
     * @return Boilr\BoilrBundle\Entity\Person
     */
    public function getInstaller()
    {
        return $this->installer;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set defaultOperationGroup
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $defaultOperationGroup
     */
    public function setDefaultOperationGroup(\Boilr\BoilrBundle\Entity\OperationGroup $defaultOperationGroup)
    {
        $this->defaultOperationGroup = $defaultOperationGroup;
    }

    /**
     * Get defaultOperationGroup
     *
     * @return Boilr\BoilrBundle\Entity\OperationGroup
     */
    public function getDefaultOperationGroup()
    {
        return $this->defaultOperationGroup;
    }

    /**
     * Set originalDate
     *
     * @param date $originalDate
     */
    public function setOriginalDate($originalDate)
    {
        $this->originalDate = $originalDate;
    }

    /**
     * Get originalDate
     *
     * @return date
     */
    public function getOriginalDate()
    {
        return $this->originalDate;
    }

    /**
     * Set contract
     *
     * @param Boilr\BoilrBundle\Entity\Contract $contract
     */
    public function setContract(\Boilr\BoilrBundle\Entity\Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Get contract
     *
     * @return Boilr\BoilrBundle\Entity\Contract
     */
    public function getContract()
    {
        return $this->contract;
    }
}
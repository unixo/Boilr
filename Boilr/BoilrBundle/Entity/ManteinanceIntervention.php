<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\ManteinanceIntervention
 *
 * @ORM\Table(name="maintenance_intervention")
 * @ORM\Entity
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
     * @var integer $status
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank
     */
    protected $status;

    /**
     * @var datetime $createDate
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    protected $createDate;

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
     */
    protected $customer;

    /**
     * @var Installer
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="installer_id", referencedColumnName="id", nullable=false)
     */
    protected $installer;

    /**
     * @var InterventionDetail
     *
     * @ORM\OneToMany(targetEntity="InterventionDetail", mappedBy="intervention")
     * @Assert\Valid
     */
    protected $details;


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
     * Set createDate
     *
     * @param datetime $createDate
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * Get createDate
     *
     * @return datetime
     */
    public function getCreateDate()
    {
        return $this->createDate;
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
}
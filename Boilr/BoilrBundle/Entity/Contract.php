<?php

namespace Boilr\BoilrBundle\Entity;

use Boilr\BoilrBundle\Validator\Constraints as MyAssert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\Contract
 *
 * @ORM\Table(name="contracts")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\ContractRepository")
 */
class Contract
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
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     */
    protected $customer;

    /**
     * @var System
     *
     * @ORM\ManyToOne(targetEntity="System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=false)
     */
    protected $system;

    /**
     * @var date $startDate
     *
     * @ORM\Column(name="start_date", type="date", nullable=false)
     * @MyAssert\CustomDate()
     */
    protected $startDate;

    /**
     * @var date $endDate
     *
     * @ORM\Column(name="end_date", type="date", nullable=false)
     * @MyAssert\CustomDate()
     */
    protected $endDate;

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
     * Set startDate
     *
     * @param date $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Get startDate
     *
     * @return date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param datetime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Get endDate
     *
     * @return datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
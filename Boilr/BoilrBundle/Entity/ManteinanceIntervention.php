<?php

namespace Boilr\BoilrBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext,
    Doctrine\ORM\Mapping as ORM,
    Gedmo\Mapping\Annotation as Gedmo;
use Boilr\BoilrBundle\Validator\Constraints as MyAssert,
    Boilr\BoilrBundle\Entity\InterventionDetail,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\OperationGroup;

/**
 * Boilr\BoilrBundle\Entity\ManteinanceIntervention
 *
 * @ORM\Table(name="maintenance_intervention")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\ManteinanceInterventionRepository")
 * @Gedmo\Timestampable
 * @Assert\Callback(methods={"isUnplannedValid"}, groups={"unplanned"})
 * @Assert\Callback(methods={"canBeClosed"}, groups={"close"})
 */
class ManteinanceIntervention
{

    const STATUS_TENTATIVE = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_CLOSED = 2;
    const STATUS_ABORTED = 3;
    const STATUS_SUSPENDED = 4;

    public static $statusDescr = array(
        self::STATUS_TENTATIVE => "Da confermare",
        self::STATUS_CONFIRMED => "Confermato",
        self::STATUS_CLOSED => 'Concluso',
        self::STATUS_ABORTED => 'Annullato',
        self::STATUS_SUSPENDED => 'Sospeso'
    );

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var datetime $scheduledDate
     *
     * @ORM\Column(name="scheduled_date", type="datetime", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     * @Assert\Date(groups={"unplanned"})
     * @MyAssert\WorkingDay(groups={"unplanned"})
     */
    protected $scheduledDate;

    /**
     * @var datetime $closeDate
     *
     * @ORM\Column(name="exp_close_date", type="datetime", nullable=false)
     * @Assert\DateTime()
     */
    protected $expectedCloseDate;

    /**
     * @var datetime $closeDate
     *
     * @ORM\Column(name="close_date", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    protected $closeDate;

    /**
     * @var hasCheckResults
     *
     * @ORM\Column(name="has_results", type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $hasCheckResults;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(groups={"unplanned"})
     */
    protected $customer;

    /**
     * @var Installer
     *
     * @ORM\ManyToOne(targetEntity="Installer")
     * @ORM\JoinColumn(name="installer_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank(groups={"flow_interventionInstallerForm_step1"})
     */
    protected $installer;

    /**
     * @var InterventionDetail
     *
     * @ORM\OneToMany(targetEntity="InterventionDetail", mappedBy="intervention", cascade={"persist"})
     * @Assert\Valid(groups={"unplanned"})
     */
    protected $details;

    /**
     *
     * @return \Boilr\BoilrBundle\Entity\ManteinanceIntervention
     */
    public static function UnplannedInterventionFactory()
    {
        $int = new ManteinanceIntervention();
        $int->setIsPlanned(false);
        $int->setHasCheckResults(false);
        $int->setStatus(self::STATUS_TENTATIVE);

        return $int;
    }

    public static function PlannedInterventionFactory(\Boilr\BoilrBundle\Entity\Contract $contract)
    {
        $int = new ManteinanceIntervention();
        $int->setHasCheckResults(false);
        $int->setIsPlanned(true);
        $int->setContract($contract);
        $int->setStatus(ManteinanceIntervention::STATUS_TENTATIVE);
        $int->setCustomer($contract->getCustomer());

        return $int;
    }

    /**
     * Add a system to be checked
     *
     * @param MySystem $sys
     * @return \Boilr\BoilrBundle\Entity\InterventionDetail
     */
    public function addSystem(MySystem $sys, OperationGroup $opGroup)
    {
        // create a new instance of InterventionDetail
        $detail = new InterventionDetail();
        $detail->setSystem($sys);
        $detail->setIntervention($this);
        $detail->setOperationGroup($opGroup);

        // Link it with this intervention
        $this->addInterventionDetail($detail);

        // Check if system comes with a default installer and intervention has no installer linked
        if ($this->getInstaller() == null) {
            if ($sys->getDefaultInstaller()) {
                $this->setInstaller($sys->getDefaultInstaller());
            }
        }

        return $detail;
    }

    /**
     * Check if this unplanned intervention is valid
     *
     * @param ExecutionContext $context
     */
    public function isUnplannedValid(ExecutionContext $context)
    {
        // Intervention date must be in the future
        $now = new \DateTime();
        if ($this->getScheduledDate() <= $now) {
            $property_path = $context->getPropertyPath() . ".scheduledDate.date";
            $context->setPropertyPath($property_path);
            $context->addViolation('Non è possibile creare un intervento nel passato', array(), null);
        }

        $oneAtLeast = false;
        foreach ($this->getDetails() as $detail) {
            if ($detail->getChecked() && $detail->getOperationGroup()) {
                $oneAtLeast = true;
                break;
            }
        }
        if (!$oneAtLeast) {
            $property_path = $context->getPropertyPath() . ".details";
            $context->setPropertyPath($property_path);
            $context->addViolation("Selezionare almeno un sistema da revisionare", array(), null);
        }
    }

    /**
     * Returns intervention status as string
     *
     * @return string
     */
    public function getStatusDescr()
    {
        return self::$statusDescr[$this->getStatus()];
    }

    public static function interventionForCustomer(MyPerson $customer)
    {
        $interv = ManteinanceIntervention::UnplannedInterventionFactory();
        $interv->setCustomer($customer);
        $interv->setScheduledDate(new \DateTime());

        foreach ($customer->getSystems() as $system) {
            $detail = new InterventionDetail();
            $detail->setIntervention($interv);
            $detail->setSystem($system);
            $interv->addInterventionDetail($detail);
        }

        return $interv;
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
     * Set installer
     *
     * @param Boilr\BoilrBundle\Entity\Installer $installer
     */
    public function setInstaller(\Boilr\BoilrBundle\Entity\Installer $installer)
    {
        $this->installer = $installer;
    }

    /**
     * Get installer
     *
     * @return Boilr\BoilrBundle\Entity\Installer
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

    /**
     * Set expectedCloseDate
     *
     * @param datetime $expectedCloseDate
     */
    public function setExpectedCloseDate($expectedCloseDate)
    {
        $this->expectedCloseDate = $expectedCloseDate;
    }

    /**
     * Get expectedCloseDate
     *
     * @return datetime
     */
    public function getExpectedCloseDate()
    {
        return $this->expectedCloseDate;
    }

    /**
     * Returns true if intervention is tentative
     *
     * @return bool
     */
    public function isTentative()
    {
        return ($this->getStatus() == self::STATUS_TENTATIVE);
    }

    /**
     * Returns true if intervention is confirmed
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return ($this->getStatus() == self::STATUS_CONFIRMED);
    }

    /**
     * Returns true if intervention was aborted
     *
     * @return bool
     */
    public function isAborted()
    {
        return ($this->getStatus() == self::STATUS_ABORTED);
    }

    /**
     * Returns true if intervention was closed
     *
     * @return bool
     */
    public function isClosed()
    {
        return ($this->getStatus() == self::STATUS_CLOSED);
    }

    /**
     * Set scheduledDate
     *
     * @param datetime $scheduledDate
     */
    public function setScheduledDate($scheduledDate)
    {
        $this->scheduledDate = $scheduledDate;
    }

    /**
     * Get scheduledDate
     *
     * @return datetime
     */
    public function getScheduledDate()
    {
        return $this->scheduledDate;
    }

    public function canBeClosed(ExecutionContext $context)
    {
        if (!$this->closeDate || $this->closeDate < $this->scheduledDate) {
            $property_path = $context->getPropertyPath() . ".closeDate";
            $context->setPropertyPath($property_path);
            $context->addViolation("La data di chiusura non è valida", array(), null);
        }
    }


    /**
     * Set hasCheckResults
     *
     * @param boolean $hasCheckResults
     */
    public function setHasCheckResults($hasCheckResults)
    {
        $this->hasCheckResults = $hasCheckResults;
    }

    /**
     * Get hasCheckResults
     *
     * @return boolean
     */
    public function getHasCheckResults()
    {
        return $this->hasCheckResults;
    }

    public function asXml()
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $rootNode = $doc->createElement('intervention');
        $rootNode->setAttribute('isPlanned', $this->isPlanned?"true":"false");
        $rootNode->setAttribute('scheduledDate', $this->scheduledDate->format('d-m-Y H:i'));
        $rootNode->setAttribute('expectedCloseDate', $this->expectedCloseDate->format('d-m-Y H:i'));
        $rootNode->setAttribute('closeDate', $this->closeDate->format('d-m-Y H:i'));
        $doc->appendChild($rootNode);

        $rootNode->appendChild($doc->importNode($this->customer->asXml(), true));
        $rootNode->appendChild($doc->importNode($this->installer->asXml(), true));

        $detailsNode = $doc->createElement("details");
        foreach ($this->details as $detail) {
            /* @var $detail \Boilr\BoilrBundle\Entity\InterventionDetail */
            $detailsNode->appendChild($doc->importNode($detail->asXml(), true));
        }
        $rootNode->appendChild($detailsNode);

        echo $doc->saveXML();die();

        return $doc;
    }
}
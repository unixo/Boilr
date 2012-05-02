<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext;

/**
 * Boilr\BoilrBundle\Entity\Person
 *
 * @ORM\Table(name="people")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\PersonRepository")
 * @Assert\Callback(methods={"isStep1Valid"}, groups={"registry", "flow_newPerson_step1"})
 */
class Person
{

    const TYPE_PHYSICAL = 1;
    const TYPE_GIURIDICAL = 2;
    const TYPE_BUILDING = 3;

    public static $typeDescr = array(
        self::TYPE_PHYSICAL => "Persona fisica",
        self::TYPE_GIURIDICAL => "Persona giurica",
        self::TYPE_BUILDING => 'Condominio'
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
     * @var string $type
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(groups={"registry", "flow_newPerson_step1"})
     */
    protected $type;

    /**
     * @var string $title
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $title;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(groups={"registry", "flow_newPerson_step1"})
     */
    protected $name;

    /**
     * @var string $surname
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(groups={"registry", "flow_newPerson_step1"})
     */
    protected $surname;

    /**
     * @var string $varCode
     *
     * @ORM\Column(name="vat_code", type="string", length=20, nullable=true)
     */
    protected $vatCode;

    /**
     * @var string $fiscalCode
     *
     * @ORM\Column(name="fiscal_code", type="string", length=20, nullable=true)
     */
    protected $fiscalCode;

    /**
     * @var string $homePhone
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $homePhone;

    /**
     * @var string $officePhone
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $officePhone;

    /**
     * @var string $cellularPhone
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $cellularPhone;

    /**
     * @var string $faxNumber
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $faxNumber;

    /**
     * @var string $primaryMail
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Email
     */
    protected $primaryMail;

    /**
     * @var string $secondaryMail
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Email
     */
    protected $secondaryMail;

    /**
     * @var $notes
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * @var $isAdministrator
     *
     * @ORM\Column(name="is_administrator", type="boolean")
     * @Assert\Type(type="bool")
     */
    protected $isAdministrator = false;

    /**
     * @var Address
     *
     * @ORM\OneToMany(targetEntity="Address", mappedBy="person", cascade={"persist"})
     * @Assert\Valid(groups={"flow_newPerson_step2"})
     */
    protected $addresses;

    /**
     * @var System
     *
     * @ORM\OneToMany(targetEntity="System", mappedBy="owner", cascade={"persist"})
     * @Assert\Valid(groups={"flow_newPerson_step3"})
     */
    protected $systems;

    /**
     * @var Contract
     *
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="customer", cascade={"persist"})
     */
    protected $contracts;

    /**
     * Magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * Returns contact type description as string
     *
     * @return string
     */
    public function getTypeDescr()
    {
        return self::$typeDescr[$this->getType()];
    }

    public function getFullName()
    {
        return sprintf("%s %s", $this->getSurname(), $this->getName());
    }

    public function __construct()
    {
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->systems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set vatCode
     *
     * @param string $vatCode
     */
    public function setVatCode($vatCode)
    {
        $this->vatCode = $vatCode;
    }

    /**
     * Get vatCode
     *
     * @return string
     */
    public function getVatCode()
    {
        return $this->vatCode;
    }

    /**
     * Set fiscalCode
     *
     * @param string $fiscalCode
     */
    public function setFiscalCode($fiscalCode)
    {
        $this->fiscalCode = $fiscalCode;
    }

    /**
     * Get fiscalCode
     *
     * @return string
     */
    public function getFiscalCode()
    {
        return $this->fiscalCode;
    }

    /**
     * Set homePhone
     *
     * @param string $homePhone
     */
    public function setHomePhone($homePhone)
    {
        $this->homePhone = $homePhone;
    }

    /**
     * Get homePhone
     *
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * Set officePhone
     *
     * @param string $officePhone
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;
    }

    /**
     * Get officePhone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Set cellularPhone
     *
     * @param string $cellularPhone
     */
    public function setCellularPhone($cellularPhone)
    {
        $this->cellularPhone = $cellularPhone;
    }

    /**
     * Get cellularPhone
     *
     * @return string
     */
    public function getCellularPhone()
    {
        return $this->cellularPhone;
    }

    /**
     * Set faxNumber
     *
     * @param string $faxNumber
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
    }

    /**
     * Get faxNumber
     *
     * @return string
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * Set primaryMail
     *
     * @param string $primaryMail
     */
    public function setPrimaryMail($primaryMail)
    {
        $this->primaryMail = $primaryMail;
    }

    /**
     * Get primaryMail
     *
     * @return string
     */
    public function getPrimaryMail()
    {
        return $this->primaryMail;
    }

    /**
     * Set secondaryMail
     *
     * @param string $secondaryMail
     */
    public function setSecondaryMail($secondaryMail)
    {
        $this->secondaryMail = $secondaryMail;
    }

    /**
     * Get secondaryMail
     *
     * @return string
     */
    public function getSecondaryMail()
    {
        return $this->secondaryMail;
    }

    /**
     * Set notes
     *
     * @param text $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Get notes
     *
     * @return text
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set isAdministrator
     *
     * @param boolean $isAdministrator
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;
    }

    /**
     * Get isAdministrator
     *
     * @return boolean
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Add addresses
     *
     * @param Boilr\BoilrBundle\Entity\Address $addresses
     */
    public function addAddress(\Boilr\BoilrBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;
    }

    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Get addresses
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add systems
     *
     * @param Boilr\BoilrBundle\Entity\System $systems
     */
    public function addSystem(\Boilr\BoilrBundle\Entity\System $systems)
    {
        $this->systems[] = $systems;
    }

    public function setSystems($systems)
    {
        $this->systems = $systems;
    }

    /**
     * Get systems
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSystems()
    {
        return $this->systems;
    }

    public function getTypeAsString()
    {
        return self::$typeDescr[$this->type];
    }

    public function isStep1Valid(ExecutionContext $context)
    {
        if (empty($this->homePhone) && empty($this->cellularPhone) && empty($this->officePhone)) {
            $property_path = $context->getPropertyPath() . ".homePhone";
            $context->setPropertyPath($property_path);
            $context->addViolation('Specificare almeno un recapito telefonico', array(), null);
        }
    }

    /**
     * Add contracts
     *
     * @param Boilr\BoilrBundle\Entity\Contract $contracts
     */
    public function addContract(\Boilr\BoilrBundle\Entity\Contract $contracts)
    {
        $this->contracts[] = $contracts;
    }

    /**
     * Get contracts
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    public function asXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $personXML = $dom->createElement("customer");
        $personXML->appendChild($dom->createElement("name", $this->name));
        $personXML->appendChild($dom->createElement("surname", $this->surname));
        $personXML->appendChild($dom->createElement("homePhone", $this->homePhone));
        $personXML->appendChild($dom->createElement("officePhone", $this->officePhone));

        return $personXML;
    }
}
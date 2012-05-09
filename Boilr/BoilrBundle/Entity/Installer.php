<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext;

/**
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\InstallerRepository")
 * @ORM\Table(name="installers")
 */
class Installer
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
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="employees")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     */
    protected $company;

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
     * @var string $email
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Email
     */
    protected $email;

    /**
     * @var $account
     *
     * @ORM\OneToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $account;

    /**
     * @var SystemType
     *
     * @ORM\ManyToMany(targetEntity="SystemType", inversedBy="installers")
     * @ORM\JoinTable(name="abilities")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $abilities;

    public function __construct()
    {
        $this->abilities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set company
     *
     * @param Boilr\BoilrBundle\Entity\Company $company
     */
    public function setCompany(\Boilr\BoilrBundle\Entity\Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get company
     *
     * @return Boilr\BoilrBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Add abilities
     *
     * @param Boilr\BoilrBundle\Entity\SystemType $abilities
     */
    public function addSystemType(\Boilr\BoilrBundle\Entity\SystemType $abilities)
    {
        $this->abilities[] = $abilities;
    }

    /**
     * Get abilities
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getAbilities()
    {
        return $this->abilities;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getFullName()
    {
        return $this->surname . ' ' . $this->name;
    }

    /**
     * Set account
     *
     * @param Boilr\BoilrBundle\Entity\User $account
     */
    public function setAccount(\Boilr\BoilrBundle\Entity\User $account)
    {
        $this->account = $account;
    }

    /**
     * Get account
     *
     * @return Boilr\BoilrBundle\Entity\User
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Returns an instance of DOMElement representing current instance
     *
     * @return DOMElement
     */
    public function asXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $installerXML = $dom->createElement("installer");
        $installerXML->appendChild($dom->createElement("name", $this->name));
        $installerXML->appendChild($dom->createElement("surname", $this->surname));
        $installerXML->appendChild($dom->createElement("officePhone", $this->officePhone));
        $installerXML->appendChild($dom->createElement("cellularPhone", $this->cellularPhone));
        $installerXML->appendChild($dom->createElement("company", $this->company->getName()));

        return $installerXML;
    }

    /**
     * Returns a comma-separated string representing installer abilities
     * (type of systems he's able to mantain)
     *
     * @return string
     */
    public function getAbilitiesDescr()
    {
        $descr = array();
        foreach ($this->abilities as $ab) {
            $descr[] = $ab->getName();
        }

        return implode(", ", $descr);
    }

    public function getPhonesDescr()
    {
        $result = array();

        if ($this->cellularPhone) {
            $result[] = sprintf("cellulare: %s", $this->cellularPhone);
        }
        if ($this->officePhone) {
            $result[] = sprintf("ufficio: %s", $this->officePhone);
        }

        return implode(", ", $result);
    }
}
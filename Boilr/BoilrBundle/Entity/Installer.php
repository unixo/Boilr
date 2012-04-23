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
     * @ORM\Id
     * @ORM\Column(type="integer")
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
     * @var SystemType
     *
     * @ORM\ManyToMany(targetEntity="SystemType", inversedBy="installers")
     * @ORM\JoinTable(name="abilities")
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
        return $this->surname. ' ' .$this->name;
    }
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Boilr\BoilrBundle\Validator\Constraints as MyAssert;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext;

/**
 * Boilr\BoilrBundle\Entity\System
 *
 * @ORM\Table(name="systems")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\SystemRepository")
 * @Assert\Callback(methods={"isSystemValid"}, groups={"system"})
 */
class System
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
     * @var SystemType
     *
     * @ORM\ManyToOne(targetEntity="SystemType", cascade={"persist"})
     * @ORM\JoinColumn(name="systemtype_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(groups={"system", "flow_newPerson_step3"})
     */
    protected $systemType;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(groups={"system", "flow_newPerson_step3"})
     */
    protected $product;

    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="Address", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * @var date $installDate
     *
     * @ORM\Column(name="install_date", type="date", nullable=true)
     * @Assert\NotBlank(groups={"system", "flow_newPerson_step3"})
     * @MyAssert\CustomDate(groups={"system", "flow_newPerson_step3"})
     */
    protected $installDate;

    /**
     * @var date $lastManteinance
     *
     * @ORM\Column(name="last_manteinance", type="date", nullable=true)
     * @MyAssert\CustomDate(groups={"system", "flow_newPerson_step3"})
     */
    protected $lastManteinance;

    /**
     * @var string $code
     *
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\NotBlank(groups={"system", "flow_newPerson_step3"})
     */
    protected $code;

    /**
     * @var string $descr
     *
     * @ORM\Column(type="string", length=150, nullable=false)
     * @Assert\NotBlank(groups={"system", "flow_newPerson_step3"})
     */
    protected $descr;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="systems")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    protected $owner;

    /**
     * @var defaultInstaller
     *
     * @ORM\ManyToOne(targetEntity="Installer")
     * @ORM\JoinColumn(name="installer_id", referencedColumnName="id", nullable=true)
     */
    protected $defaultInstaller;

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
     * Set installDate
     *
     * @param date $installDate
     */
    public function setInstallDate($installDate)
    {
        $this->installDate = $installDate;
    }

    /**
     * Get installDate
     *
     * @return date
     */
    public function getInstallDate()
    {
        return $this->installDate;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set descr
     *
     * @param string $descr
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
    }

    /**
     * Get descr
     *
     * @return string
     */
    public function getDescr()
    {
        return $this->descr;
    }

    /**
     * Set product
     *
     * @param Boilr\BoilrBundle\Entity\Product $product
     */
    public function setProduct(\Boilr\BoilrBundle\Entity\Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return Boilr\BoilrBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set owner
     *
     * @param Boilr\BoilrBundle\Entity\Person $owner
     */
    public function setOwner(\Boilr\BoilrBundle\Entity\Person $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return Boilr\BoilrBundle\Entity\Person
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set lastManteinance
     *
     * @param date $lastManteinance
     */
    public function setLastManteinance($lastManteinance)
    {
        $this->lastManteinance = $lastManteinance;
    }

    /**
     * Get lastManteinance
     *
     * @return date
     */
    public function getLastManteinance()
    {
        return $this->lastManteinance;
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

    /**
     * Check if this system is covered by an assistance plan
     *
     * @return boolean
     */
    public function isUnderAssistance()
    {
        $success   = false;
        $owner     = $this->getOwner();

        if ($owner) {
            $contracts = $owner->getContracts();

            foreach ($contracts as $contract) {
                /* @var $contract Boilr\BoilrBundle\Entity\Contract */
                if ($contract->getSystem()->getId() == $this->getId()) {
                    $success = true;
                    break;
                }
            }
        }

        return $success;
    }

    /**
     * Set address
     *
     * @param Boilr\BoilrBundle\Entity\Address $address
     */
    public function setAddress(\Boilr\BoilrBundle\Entity\Address $address)
    {
        $this->address = $address;
    }

    /**
     * Get address
     *
     * @return Boilr\BoilrBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    public function isSystemValid(ExecutionContext $context)
    {
        $now = new \DateTime();

        if ($this->getInstallDate() > $now) {
            $property_path = $context->getPropertyPath() . ".installDate";
            $context->setPropertyPath($property_path);
            $context->addViolation("La data d'installazione è successiva a quella odierna", array(), null);

            return;
        }

        if ($this->getLastManteinance() > $now) {
            $property_path = $context->getPropertyPath() . ".lastManteinance";
            $context->setPropertyPath($property_path);
            $context->addViolation("La data d'ultima manutenzione è successiva a quella odierna", array(), null);

            return;
        }

        if ($this->getInstallDate() > $this->getLastManteinance()) {
            $property_path = $context->getPropertyPath() . ".lastManteinance";
            $context->setPropertyPath($property_path);
            $context->addViolation("La data d'installazione è successiva all'ultima manutenzione", array(), null);
        }
    }

    /**
     * Set defaultInstaller
     *
     * @param Boilr\BoilrBundle\Entity\Installer $defaultInstaller
     */
    public function setDefaultInstaller(\Boilr\BoilrBundle\Entity\Installer $defaultInstaller)
    {
        $this->defaultInstaller = $defaultInstaller;
    }

    /**
     * Get defaultInstaller
     *
     * @return Boilr\BoilrBundle\Entity\Installer
     */
    public function getDefaultInstaller()
    {
        return $this->defaultInstaller;
    }
}
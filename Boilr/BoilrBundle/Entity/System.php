<?php

namespace Boilr\BoilrBundle\Entity;

use Boilr\BoilrBundle\Validator\Constraints as MyAssert;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\System
 *
 * @ORM\Table(name="systems")
 * @ORM\Entity
 * @Gedmo\Timestampable
 */
class System
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
     * @var date $installDate
     *
     * @ORM\Column(name="install_date", type="date", nullable=true)
     * @MyAssert\CustomDate(pattern="/^(\d{2})-(\d{2})-(\d{4})$/", groups={"system", "flow_newPerson_step3"})
     */
    protected $installDate;

    /**
     * @var date $lastManteinance
     *
     * @ORM\Column(name="last_manteinance", type="date", nullable=true)
     * @MyAssert\CustomDate(pattern="/^(\d{2})-(\d{2})-(\d{4})$/", groups={"system", "flow_newPerson_step3"})
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
        $contracts = $this->getOwner()->getContracts();

        return $contracts->contains($this);
    }
}
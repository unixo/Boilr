<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Vich\GeographicalBundle\Annotation as Vich;

/**
 * Boilr\BoilrBundle\Entity\Company
 *
 * @ORM\Entity
 * @ORM\Table(name="companies")
 * @Vich\Geographical(on="update")
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string $varCode
     *
     * @ORM\Column(name="vat_code", type="string", length=20, nullable=true)
     */
    protected $vatCode;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @Assert\NotBlank
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @Assert\NotBlank
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @Assert\NotBlank
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank
     */
    private $state;

    /**
     * @var decimal $longitude
     *
     * @ORM\Column(type="decimal", scale="7", nullable=true)
     */
    private $longitude;

    /**
     * @var decimal $latitude
     *
     * @ORM\Column(type="decimal", scale="7", nullable=true)
     */
    private $latitude;

    /**
     * @var Person
     *
     * @ORM\OneToMany(targetEntity="Person", mappedBy="company", cascade={"persist", "remove"})
     */
    protected $employees;

    /**
     * @Vich\GeographicalQuery
     *
     * This method builds the full address to query for coordinates.
     */
    public function getAddress()
    {
        return sprintf(
                        '%s, %s, %s %s', $this->street, $this->city, $this->state, $this->postalCode
        );
    }

    public function __construct()
    {
        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add employees
     *
     * @param Boilr\BoilrBundle\Entity\Person $employees
     */
    public function addPerson(\Boilr\BoilrBundle\Entity\Person $employees)
    {
        $this->employees[] = $employees;
    }

    /**
     * Get employees
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
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
     * Set street
     *
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set province
     *
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set state
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set longitude
     *
     * @param decimal $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return decimal
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param decimal $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return decimal
     */
    public function getLatitude()
    {
        return $this->latitude;
    }
}
<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\GeographicalBundle\Annotation as Vich;

/**
 * Boilr\BoilrBundle\Entity\Address
 *
 * @ORM\Table(name="addresses")
 * @ORM\Entity
 * @Vich\Geographical(on="update")
 */
class Address
{

    const TYPE_HOME = 1;
    const TYPE_OFFICE = 2;
    const TYPE_OTHER = 3;

    private static $typeDescr = array(
        self::TYPE_HOME => "Abitazione",
        self::TYPE_OFFICE => "Ufficio",
        self::TYPE_OTHER => "Altro"
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
     * @var integer $type
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\NotBlank(groups={"address", "flow_newPerson_step2"})
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
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="addresses")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     */
    private $person;

    public function typeAsString()
    {
        return self::$typeDescr[$this->getType()];
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
     * Set longitude
     *
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set person
     *
     * @param Boilr\BoilrBundle\Entity\Person $person
     */
    public function setPerson(\Boilr\BoilrBundle\Entity\Person $person)
    {
        $this->person = $person;
    }

    /**
     * Get person
     *
     * @return Boilr\BoilrBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

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
     * Returns true if latitude/longitude are not null
     *
     * @return boolean
     */
    public function isValid()
    {
        return (($this->latitude != 0) && ($this->longitude != 0));
    }

    /**
     * Returns an instance of DOMElement representing current instance
     *
     * @return DOMElement
     */
    public function asXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $addressXML = $dom->createElement("address");
        $addressXML->appendChild($dom->createElement("city", $this->city));
        $addressXML->appendChild($dom->createElement("street", $this->street));
        $addressXML->appendChild($dom->createElement("province", $this->province));
        $addressXML->appendChild($dom->createElement("postalCode", $this->postalCode));
        $addressXML->appendChild($dom->createElement("state", $this->state));

        return $addressXML;
    }

    public function getGeoPosition()
    {
        return new \Boilr\BoilrBundle\Service\GeoPosition($this->latitude, $this->longitude);
    }
}
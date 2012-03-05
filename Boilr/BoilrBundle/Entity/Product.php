<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 * @Gedmo\Timestampable
 */
class Product
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
     * @var Manufacturer
     *
     * @ORM\ManyToOne(targetEntity="Manufacturer")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", nullable=false)
     */
    protected $manufacturer;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=30, nullable=false)
     * @Assert\NotBlank
     */
    protected $name;

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
     * Set manufacturer
     *
     * @param Boilr\BoilrBundle\Entity\Manufacturer $manufacturer
     */
    public function setManufacturer(\Boilr\BoilrBundle\Entity\Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Get manufacturer
     *
     * @return Boilr\BoilrBundle\Entity\Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
}
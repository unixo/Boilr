<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\OperationGroup
 *
 * @ORM\Table(name="operation_groups")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\OperationGroupRepository")
 */
class OperationGroup
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string $descr
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    protected $descr;

    /**
     * @var TemplateItem
     *
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="parentGroup")
     * @ORM\OrderBy({"listOrder" = "ASC"})
     */
    protected $operations;

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

    public function getFullDescr()
    {
        return sprintf("%s (%s)", $this->getDescr(), $this->getName());
    }
    public function __construct()
    {
        $this->operations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add operations
     *
     * @param Boilr\BoilrBundle\Entity\Operation $operations
     */
    public function addOperation(\Boilr\BoilrBundle\Entity\Operation $operations)
    {
        $this->operations[] = $operations;
    }

    /**
     * Get operations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }
}
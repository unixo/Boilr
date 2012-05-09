<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;
use Boilr\BoilrBundle\Entity\OperationGroup;

/**
 * Boilr\BoilrBundle\Entity\Operation
 *
 * @ORM\Entity
 * @ORM\Table(name="operations")
 */
class Operation
{

    const RESULT_CHECKBOX = 1;
    const RESULT_NOTE = 2;

    public static $resultDescr = array(
        self::RESULT_CHECKBOX => "Si/No/N.C.",
        self::RESULT_NOTE => "Campo note",
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var integer $resultType
     *
     * @ORM\Column(name="result_type", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $resultType = self::RESULT_CHECKBOX;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToMany(targetEntity="OperationGroup", mappedBy="operations")
     * @Assert\NotBlank
     */
    protected $parentGroups;

    public function getResultTypeDescr()
    {
        return self::$resultDescr[$this->getResultType()];
    }

    public function __construct()
    {
        $this->parentGroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set resultType
     *
     * @param integer $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    /**
     * Get resultType
     *
     * @return integer
     */
    public function getResultType()
    {
        return $this->resultType;
    }

    /**
     * Add parentGroups
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $parentGroups
     */
    public function addOperationGroup(\Boilr\BoilrBundle\Entity\OperationGroup $parentGroups)
    {
        $this->parentGroups[] = $parentGroups;
    }

    /**
     * Get parentGroups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getParentGroups()
    {
        return $this->parentGroups;
    }
}
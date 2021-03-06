<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext;
use Boilr\BoilrBundle\Entity\Operation as MyOperation;

/**
 * Boilr\BoilrBundle\Entity\InterventionCheck
 *
 * @ORM\Table(name="intervention_checks")
 * @ORM\Entity
 * @Assert\Callback(methods={"isCheckValid"})
 */
class InterventionCheck
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
     * @var InterventionDetail
     *
     * @ORM\ManyToOne(targetEntity="InterventionDetail")
     * @ORM\JoinColumn(name="detail_id", referencedColumnName="id", nullable=false)
     */
    protected $parentDetail;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var integer $resultType
     *
     * @ORM\Column(name="result_type", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $resultType;

    /**
     * @var string $textValue
     *
     * @ORM\Column(name="text_value", type="string", length=255, nullable=true)
     */
    protected $textValue;

    /**
     * @var integer $threewayValue
     *
     * @ORM\Column(name="threeway_value", type="integer", nullable=true)
     */
    protected $threewayValue;

    public function isCheckValid(ExecutionContext $context)
    {
        if ($this->getResultType() == MyOperation::RESULT_CHECKBOX) {
            if ($this->threewayValue === null || !in_array($this->threewayValue, array(0,1,2))) {
                $property_path = $context->getPropertyPath() . ".threewayValue";
                $context->setPropertyPath($property_path);
                $context->addViolation("Specificare il risultato dell'ispezione", array(), null);
            }
        } else {
            if ($this->textValue === null || strlen($this->textValue) == 0) {
                $property_path = $context->getPropertyPath() . ".textValue";
                $context->setPropertyPath($property_path);
                $context->addViolation("Specificare il risultato dell'ispezione", array(), null);
            }
        }
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
     * Set textValue
     *
     * @param string $textValue
     */
    public function setTextValue($textValue)
    {
        $this->textValue = $textValue;
    }

    /**
     * Get textValue
     *
     * @return string
     */
    public function getTextValue()
    {
        $str= $this->textValue?$this->textValue:"";
        // @todo clean up
        return $str;
    }

    /**
     * Set threewayValue
     *
     * @param integer $threewayValue
     */
    public function setThreewayValue($threewayValue)
    {
        $this->threewayValue = $threewayValue;
    }

    /**
     * Get threewayValue
     *
     * @return integer
     */
    public function getThreewayValue()
    {
        return $this->threewayValue;
    }

    /**
     * Set parentDetail
     *
     * @param Boilr\BoilrBundle\Entity\InterventionDetail $parentDetail
     */
    public function setParentDetail(\Boilr\BoilrBundle\Entity\InterventionDetail $parentDetail)
    {
        $this->parentDetail = $parentDetail;
    }

    /**
     * Get parentDetail
     *
     * @return Boilr\BoilrBundle\Entity\InterventionDetail
     */
    public function getParentDetail()
    {
        return $this->parentDetail;
    }

    /**
     * Returns an instance of DOMElement representing current instance
     *
     * @return DOMElement
     */
    public function asXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $checkXML = $dom->createElement("check");
        $checkXML->appendChild($dom->createElement("operation", $this->getName()));
        if ($this->getResultType() === Operation::RESULT_CHECKBOX) {
            $checkXML->appendChild($dom->createElement("restype", "checkbox"));
            $value = "";
            switch ($this->threewayValue) {
                case 0:
                    $value = "NO";
                    break;
                case 1:
                    $value = "SI";
                    break;
                case 2:
                    $value = "Non Controllato";
                    break;
            }
            $checkXML->appendChild($dom->createElement("value", $value));
        } else {
            $checkXML->appendChild($dom->createElement("restype", "text"));
            $checkXML->appendChild($dom->createElement("value", $this->textValue));
        }

        return $checkXML;
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
}
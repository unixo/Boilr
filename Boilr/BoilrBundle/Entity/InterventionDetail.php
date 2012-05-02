<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\ExecutionContext,
    Gedmo\Mapping\Annotation as Gedmo;

/**
 * Boilr\BoilrBundle\Entity\InterventionDetail
 *
 * @ORM\Table(name="intervention_detail", uniqueConstraints={
 * @ORM\UniqueConstraint(name="sys_int_idx", columns={"intervention_id", "system_id"})})
 * @ORM\Entity
 * @Assert\Callback(methods={"isDetailValid"}, groups={"unplanned"})
 */
class InterventionDetail
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
     * @var ManteinanceIntervention
     *
     * @ORM\ManyToOne(targetEntity="ManteinanceIntervention")
     * @ORM\JoinColumn(name="intervention_id", referencedColumnName="id", nullable=false)
     */
    protected $intervention;

    /**
     * @var System
     *
     * @ORM\ManyToOne(targetEntity="System")
     * @ORM\JoinColumn(name="system_id", referencedColumnName="id", nullable=false)
     */
    protected $system;

    /**
     * @var OperationGroup
     *
     * @ORM\ManyToOne(targetEntity="OperationGroup")
     * @ORM\JoinColumn(name="op_group_id", referencedColumnName="id", nullable=false)
     */
    protected $operationGroup;

    /**
     * @var InterventionCheck
     *
     * @ORM\OneToMany(targetEntity="InterventionCheck", mappedBy="parentDetail", cascade={"persist"})
     */
    protected $checks = array();

    /**
     * WARNING: this field is not bound to any column on DB!!
     * It's used by some forms to select more than one system at time
     *
     * @var bool $checked
     */
    protected $checked;

    public function __construct()
    {
        $this->checks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set intervention
     *
     * @param Boilr\BoilrBundle\Entity\ManteinanceIntervention $intervention
     */
    public function setIntervention(\Boilr\BoilrBundle\Entity\ManteinanceIntervention $intervention)
    {
        $this->intervention = $intervention;
    }

    /**
     * Get intervention
     *
     * @return Boilr\BoilrBundle\Entity\ManteinanceIntervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }

    /**
     * Set system
     *
     * @param Boilr\BoilrBundle\Entity\System $system
     */
    public function setSystem(\Boilr\BoilrBundle\Entity\System $system)
    {
        $this->system = $system;
    }

    /**
     * Get system
     *
     * @return Boilr\BoilrBundle\Entity\System
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * Set operationGroup
     *
     * @param Boilr\BoilrBundle\Entity\OperationGroup $operationGroup
     */
    public function setOperationGroup(\Boilr\BoilrBundle\Entity\OperationGroup $operationGroup)
    {
        $this->operationGroup = $operationGroup;
    }

    /**
     * Get operationGroup
     *
     * @return Boilr\BoilrBundle\Entity\OperationGroup
     */
    public function getOperationGroup()
    {
        return $this->operationGroup;
    }

    /**
     * Add checks
     *
     * @param Boilr\BoilrBundle\Entity\InterventionCheck $checks
     */
    public function addInterventionCheck(\Boilr\BoilrBundle\Entity\InterventionCheck $checks)
    {
        $this->checks[] = $checks;
    }

    /**
     * Get checks
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChecks()
    {
        return $this->checks;
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

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    public function isDetailValid(ExecutionContext $context)
    {
        // Intervention date must be in the future
        if ($this->getChecked() && (! $this->getOperationGroup()) ) {
            $property_path = $context->getPropertyPath() . ".operationGroup";
            $context->setPropertyPath($property_path);
            $context->addViolation('Specificare la tipologia di controllo', array(), null);
        }
    }

    /**
     * Returns an instance of DOMElement representing current instance
     *
     * @return DOMElement
     */
    public function asXml()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $detailXML = $dom->createElement("detail");
        $detailXML->appendChild($dom->importNode($this->system->asXml(), true));
        $checks = $dom->createElement("checks");
        foreach ($this->checks as $check) {
            $checks->appendChild($dom->importNode($check->asXml(), true));
        }
        $detailXML->appendChild($checks);

        return $detailXML;
    }
}
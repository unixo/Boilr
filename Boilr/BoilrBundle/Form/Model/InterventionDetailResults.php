<?php

namespace Boilr\BoilrBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Boilr\BoilrBundle\Entity\InterventionCheck,
    Boilr\BoilrBundle\Entity\InterventionDetail;

/**
 * Description of MaintenanceInterventionResult
 *
 * @author unixo
 */
class InterventionDetailResults
{

    /**
     * @var \Boilr\BoilrBundle\Entity\InterventionDetail $interventionDetail
     */
    protected $interventionDetail;

    /**
     * @var array $checks
     *
     * @Assert\Valid
     */
    protected $checks = array();

    function __construct(InterventionDetail $interventionDetail)
    {
        $this->interventionDetail = $interventionDetail;
        $this->prepareResults();
    }

    protected function prepareResults()
    {
        $operations = $this->interventionDetail->getOperationGroup()->getOperations();

        foreach ($operations as $oper) {
            /* @var $oper \Boilr\BoilrBundle\Entity\Operation */
            
            $check = new InterventionCheck();
            $check->setName($oper->getName());
            $check->setResultType($oper->getResultType());
            $check->setParentDetail($this->interventionDetail);

            $this->checks[] = $check;
        }
    }

    public function getInterventionDetail()
    {
        return $this->interventionDetail;
    }

    /**
     * @return \Boilr\BoilrBundle\Entity\System
     */
    public function getSystem()
    {
        return $this->interventionDetail->getSystem();
    }

    public function getChecks()
    {
        return $this->checks;
    }

    public function setChecks($checks)
    {
        $this->checks = $checks;
    }

}

<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Form\Model\InstallerForIntervention;

class PolicyResult
{
    const RESULT_INSTALLER = 1;
    const RESULT_LOAD = 2;
    const RESULT_SCHEDULE_TIME = 3;

    /**
     * Applied policy name
     *
     * @var string
     */
    protected $policyName;

    /**
     * Applied policy description
     *
     * @var string
     */
    protected $policyDescr;

    /**
     * Assignment policy result
     *
     * @var array
     */
    protected $associations = array();

    protected $resultType;

    public function getResultType()
    {
        return $this->resultType;
    }

    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    public function getAssociations()
    {
        return $this->associations;
    }

    public function setAssociations(array $associations)
    {
        $this->associations = $associations;
    }

    public function addAssociation(InstallerForIntervention $entry)
    {
        $this->associations[] = $entry;
    }

    public function getPolicyName()
    {
        return $this->policyName;
    }

    public function setPolicyName($policyName)
    {
        $this->policyName = $policyName;
    }

    public function getPolicyDescr()
    {
        return $this->policyDescr;
    }

    public function setPolicyDescr($policyDescr)
    {
        $this->policyDescr = $policyDescr;
    }

    public function getPolicyClass()
    {
        return $this->policyClass;
    }

    public function setPolicyClass($policyClass)
    {
        $this->policyClass = $policyClass;
    }

}

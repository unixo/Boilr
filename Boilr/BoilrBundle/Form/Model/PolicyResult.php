<?php

namespace Boilr\BoilrBundle\Form\Model;


class PolicyResult
{

    protected $policyName;
    protected $policyDescr;
    protected $policyClass;
    protected $associations = array();

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

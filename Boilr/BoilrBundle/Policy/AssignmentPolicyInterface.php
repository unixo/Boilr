<?php

namespace Boilr\BoilrBundle\Policy;

interface AssignmentPolicyInterface
{

    public function setInstallers($installers = array());

    public function setInterventions($interventions = array());

    public function elaborate();
}
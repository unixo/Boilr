<?php

namespace Boilr\BoilrBundle\Policy;

interface AssignmentPolicyInterface
{

    public function setInstallers($installers = array());

    public function setInterventions($interventions = array());

    public function elaborate();

    public function getResult();

    /**
     * Returns name of assignment policy
     *
     * @return string
     */
    public static function getName();

    /**
     * Returns policy long description
     *
     * @return string
     */
    public static function getDescription();
}
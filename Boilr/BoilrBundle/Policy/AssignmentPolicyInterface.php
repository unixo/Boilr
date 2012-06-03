<?php

namespace Boilr\BoilrBundle\Policy;

interface AssignmentPolicyInterface
{

    /**
     * Set list of available installers
     *
     * @param array $installers
     */
    public function setInstallers($installers = array());

    /**
     * Set list of available interventions
     *
     * @param array $interventions
     */
    public function setInterventions($interventions = array());

    /**
     * Elaborate installer/intervention lists
     */
    public function elaborate();

    /**
     * Apply modification to policy results. Returns false if exception raised
     * during operation.
     *
     * @param  PolicyResult $result
     * @return boolean
     */
    public function apply(PolicyResult $result);

    /**
     * Get result of policy elaboration
     *
     * @return PolicyResult
     */
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

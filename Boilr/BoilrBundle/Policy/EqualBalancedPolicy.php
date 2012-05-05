<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\System;

/**
 * Description of EqualBalancedPolicy
 *
 * @author unixo
 */
class EqualBalancedPolicy implements AssignmentPolicyInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $installers = array();
    protected $interventions = array();

    function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function elaborate()
    {
        $results = array();

        foreach ($this->interventions as $intervention) {
            $details = $intervention->getDetails();
            $system = $details[0]->getSystem();

            $index = $this->findInstallerForSystem($system);
            $this->installers[$index]['load']++;

            $resultEntry['intervention'] = $intervention;
            $resultEntry['installer'] = $this->installers[$index];
            $results[] = $resultEntry;
        }

        //ladybug_dump($results);die();

        return $results;
    }

    public function setInstallers($installers = array())
    {
        foreach ($installers as $inst) {
            $count = $this->entityManager->getRepository('BoilrBundle:Installer')->getLoadForInstaller($inst);

            $entry['obj'] = $inst;
            $entry['load'] = $count;
            $entry['prevLoad'] = (integer) $count;

            $this->installers[] = $entry;
        }
    }

    public function setInterventions($interventions = array())
    {
        $this->interventions = $interventions;
    }

    private function findInstallerForSystem(System $system)
    {
        $systemType = $system->getSystemType();
        $minLoad = PHP_INT_MAX;
        $index = -1;

        for ($i=0; $i<count($this->installers); $i++) {
            $installer = $this->installers[$i];

            if ($systemType->getInstallers()->contains($installer['obj'])) {
                if ($installer['load'] < $minLoad) {
                    $minLoad = $installer['load'];
                    $index = $i;
                }
            }
        }

        return $index;
    }

}

<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\System,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Form\Model\InstallerForIntervention;

/**
 * Description of EqualBalancedPolicy
 *
 * @author unixo
 */
class EqualBalancedPolicy extends BasePolicy
{

    public static function getName()
    {
        return "policy_equal";
    }

    public static function getDescription()
    {
        return "Assegnazione bilanciata degli interventi";
    }

    public function elaborate()
    {
        foreach ($this->interventions as $day => $interventions) {
            foreach ($interventions as $intervention) {
                $system = $intervention->getFirstSystem();
                $destination = $system->getAddress()->getGeoPosition();

                $this->log('Intervento #' . $intervention->getId() .
                        ' Data: ' . $intervention->getScheduledDate()->format('d-m-Y H:i') .
                        ' Tipo impianto:' . $system->getSystemType()->getName()
                );

                $assoc = null;
                $installers = $this->findInstallerForSystem($system);
                foreach ($installers as $entry) {
                    $installer = $entry['obj'];
                    $this->log('valuto il tecnico: ' . $installer->getFullName(). " - LOAD: ". $entry['load']);

                    $position = $this->whereIsInstallerInDate($installer, $intervention->getScheduledDate());
                    $this->log('il tecnico è '.$position['where'].' e finisce alle: '.$position['when']->format('d-m-Y H:i'));
                    $x = $this->directionHelper->getDirections($position['where'], $destination);
                    $this->log('tempo necessario per lo spostamento: '.$x['length']);

                    $newDate = $position['when']->add(\DateInterval::createFromDateString($x['length']));
                    if ($newDate->format('U') > $intervention->getScheduledDate()->format('U')) {
                        $this->log('tecnico scartato, troppo lontano (arriverebbe alle '.$newDate->format('d-m-Y H:i').')');
                        continue;
                    }

                    $index = array_search($entry, $this->installers, true);
                    $this->installers[$index]['load']++;

                    $assoc = new InstallerForIntervention($entry['obj'], $intervention);
                    $assoc->setPrevLoad($entry['prevLoad']);
                    $assoc->setNewLoad($entry['load']+1);
                    $this->result->addAssociation($assoc);
                    break;
                }
                if ($assoc === null) {
                    $this->log('non ho trovato alcun tecnico');
                } else {
                    $this->log('tecnico selezionato: '.$assoc->getInstaller()->getFullName());
                }
                $this->log('--------------------------------------');
            }
        }

        return $this;
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
        $availableInstallers = array();

        // find all installers able to maintan given system
        for ($i = 0; $i < count($this->installers); $i++) {
            $installer = $this->installers[$i];

            if ($systemType->getInstallers()->contains($installer['obj'])) {
                $availableInstallers[] = $installer;
            }
        }

        // sort them by current work load
        usort($availableInstallers, array("\Boilr\BoilrBundle\Policy\EqualBalancedPolicy", "sortInstallersByLoad"));

        return $availableInstallers;
    }

    static function sortInstallersByLoad($i1, $i2)
    {
        $load1 = $i1['load'];
        $load2 = $i2['load'];

        if ($load1 === $load2) {
            return 0;
        }

        return ($load1<$load2)?-1:1;
    }

}

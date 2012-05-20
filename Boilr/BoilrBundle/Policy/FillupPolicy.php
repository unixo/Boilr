<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Form\Model\InstallerForIntervention;

/**
 * Description of WaypointPolicy
 *
 * @author unixo
 */
class FillupPolicy extends BasePolicy
{

    protected $installers = array();
    protected $interventions = array();

    public function elaborate()
    {
        foreach ($this->installers as $installer) {
            /* @var $installer \Boilr\BoilrBundle\Entity\Installer */

            $abilities = $installer->getAbilities();
            $this->log('Tecnico: ' . $installer->getFullName(). ' (#'.$abilities->count().' abilità)');

            $interventions = array_filter($this->interventions, function ($entry) use ($abilities) {
                        $sysType = $entry->getFirstSystem()->getSystemType();
                        return $abilities->contains($sysType);
                    }
            );
            $this->log(count($interventions).'/'.count($this->interventions).' interventi compatibili');

            foreach ($interventions as $interv) {
                $this->log('Intervento #'.$interv->getId().', alle '.$interv->getScheduledDate()->format('d-m-Y H:i'));
                $position = $this->whereIsInstallerInDate($installer, $interv->getScheduledDate());

                $system = $interv->getFirstSystem();
                $destination = $system->getAddress()->getGeoPosition();
                $x = $this->directionHelper->getDirections($position['where'], $destination);

                $newDate = $position['when']->add(\DateInterval::createFromDateString($x['length']));
                if ($newDate->format('U') < $interv->getScheduledDate()->format('U')) {
                    $assoc = new InstallerForIntervention($installer, $interv);
                    $this->result->addAssociation($assoc);

                    $count = count($this->interventions);
                    $index = array_search($interv, $this->interventions);
                    unset($this->interventions[$index]);

                    $this->log('intervento associato - '.count($this->interventions).'/'.$count);

                } else {
                    $this->log('troppo lontano, arriverebbe alle '.$newDate->format('d-m-Y H:i'));
                }
            }
        }

        return $this;
    }

    public function setInstallers($installers = array())
    {
        $this->installers = $installers;
    }

    public function setInterventions($interventions = array())
    {
        foreach ($interventions as $day => $events) {
            $this->interventions = array_merge($events, $this->interventions);
        }
    }

    public static function getDescription()
    {
        return "Priorità ad saturare un tecnico";
    }

    public static function getName()
    {
        return "policy_fillup";
    }

}

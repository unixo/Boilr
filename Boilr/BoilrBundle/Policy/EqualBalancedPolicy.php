<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\System,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Form\Model\InstallerForIntervention,
    Boilr\BoilrBundle\Form\Model\PolicyResult,
    Boilr\BoilrBundle\Service\GoogleDirection,
    Boilr\BoilrBundle\Service\GeoPosition;

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
    protected $logger;
    protected $installers = array();
    protected $interventions = array();

    /**
     * @var \Boilr\BoilrBundle\Form\Model\PolicyResult
     */
    protected $result;

    function __construct($entityManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

        $this->result = new PolicyResult();
        $this->result->setPolicyName(EqualBalancedPolicy::getName());
        $this->result->setPolicyDescr(EqualBalancedPolicy::getDescription());
    }

    public function elaborate()
    {
        $gDirection = new GoogleDirection();

        foreach ($this->interventions as $day => $interventions) {
            foreach ($interventions as $intervention) {
                $system = $intervention->getFirstSystem();
                $destination = $system->getAddress()->getGeoPosition();

                $this->logger->info('[BOILR] Intervento #' . $intervention->getId() .
                        ' Data: ' . $intervention->getScheduledDate()->format('d-m-Y H:i') .
                        ' Tipo impianto:' . $system->getSystemType()->getName()
                );

                $assoc = null;
                $installers = $this->findInstallerForSystem($system);
                foreach ($installers as $entry) {
                    $installer = $entry['obj'];
                    $this->logger->info('[BOILR] valuto il tecnico: ' . $installer->getFullName(). " - LOAD: ". $entry['load']);

                    $position = $this->whereIsInstallerInDate($installer, $intervention->getScheduledDate());
                    $this->logger->info('[BOILR] il tecnico è '.$position['where'].' e finisce alle: '.$position['when']->format('d-m-Y H:i'));
                    $x = $gDirection->getDirections($position['where'], $destination);
                    $this->logger->info('[BOILR] tempo necessario per lo spostamento: '.$x['length']);

                    $newDate = $position['when']->add(\DateInterval::createFromDateString($x['length']));
                    if ($newDate->format('U') > $intervention->getScheduledDate()->format('U')) {
                        $this->logger->info('[BOILR] tecnico scartato, troppo lontano (arriverebbe alle '.$newDate->format('d-m-Y H:i').')');
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
                    $this->logger->info('[BOILR] non ho trovato alcun tecnico');
                } else {
                    $this->logger->info('[BOILR] tecnico selezionato: '.$assoc->getInstaller()->getFullName());
                }
                $this->logger->info('[BOILR]  --------------------------------------');
            }
        }

        return $this->result;
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

    static function sortInterventionsByDate(InstallerForIntervention $i1, InstallerForIntervention $i2)
    {
        $date1 = $i1->getIntervention()->getExpectedCloseDate();
        $date2 = $i2->getIntervention()->getExpectedCloseDate();

        if ($date1 == $date2) {
            return 0;
        }

        return ($date1 < $date2) ? -1 : 1;
    }

    protected function whereIsInstallerInDate(Installer $installer, $aDate)
    {
        $params = array(
            'inst' => $installer,
            'status' => MaintenanceIntervention::STATUS_CONFIRMED,
            'aDate' => $aDate->format('d-m-Y'),
        );
        $interventions = $this->entityManager->createQuery(
                                "SELECT mi FROM BoilrBundle:MaintenanceIntervention mi " .
                                "WHERE mi.installer = :inst AND mi.status = :status AND " .
                                "DATE_FORMAT(mi.scheduledDate, '%d-%m-%Y') = :aDate " .
                                "ORDER BY mi.expectedCloseDate ASC")
                        ->setParameters($params)->getResult();

        // get all associations related to given installer
        $associations = array();
        foreach ($this->result->getAssociations() as $tmpAssoc) {
            if ($tmpAssoc->getInstaller() == $installer) {
                $tmpDate = $tmpAssoc->getIntervention()->getScheduledDate()->format('d-m-Y');
                if ($tmpDate == $aDate->format('d-m-Y')) {
                    $associations[] = $tmpAssoc;
                }
            }
        }
        if (($interv = array_pop($interventions))) {
            $associations[] = new InstallerForIntervention($interv->getInstaller(), $interv);
        }

        /*
        echo '<hr><h3>inizio</h3>';
        var_dump($aDate);
        foreach ($associations as $value) {
            var_dump($value->getIntervention()->getExpectedCloseDate()->format('d-m-Y'));
        }
        echo 'fine<br><hr>';
        */

        // If no interventions were found, installer is at company address
        $position = null;
        if (count($associations) == 0) {
            $where = new GeoPosition($installer->getCompany()->getLatitude(), $installer->getCompany()->getLongitude());
            $date = new \DateTime();
            $date->setDate(1970, 1, 1);

            $position = array('where' => $where, 'when' => $date);
        } else {
            // sort associations by date
            usort($associations, array("\Boilr\BoilrBundle\Policy\EqualBalancedPolicy", "sortInterventionsByDate"));

            $lastAssoc = $associations[0];
            $address = $lastAssoc->getIntervention()->getFirstSystem()->getAddress();

            $where = new GeoPosition($address->getLatitude(), $address->getLongitude());
            $date = $lastAssoc->getIntervention()->getExpectedCloseDate();

            $position = array('where' => $where, 'when' => $date);
        }

        return $position;
    }

    public static function getName()
    {
        return "policy_equal";
    }

    public static function getDescription()
    {
        return "Assegnazione bilanciata degli interventi";
    }

}

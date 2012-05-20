<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Service\GoogleDirection,
    Boilr\BoilrBundle\Service\GeoPosition;

/**
 * Description of BasePolicy
 *
 * @author unixo
 */
abstract class BasePolicy implements AssignmentPolicyInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Boilr\BoilrBundle\Policy\PolicyResult
     */
    protected $result;
    protected $installers = array();
    protected $interventions = array();
    protected $logger;

    function __construct($entityManager, $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

        $this->result = new PolicyResult();
        $this->result->setPolicyName($this->getName());
        $this->result->setPolicyDescr($this->getDescription());
    }

    public function getResult()
    {
        $assocs = $this->result->getAssociations();
        usort($assocs, array("\Boilr\BoilrBundle\Form\Model\InstallerForIntervention", "sortByScheduledDate"));
        $this->result->setAssociations($assocs);

        return $this->result;
    }

    public function log($message)
    {
        $this->logger->info('[BOILR] ' . $message);
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

}

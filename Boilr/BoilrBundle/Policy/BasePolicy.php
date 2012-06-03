<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Form\Model\InstallerForIntervention,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Service\GeoDirectionInterface,
    Boilr\BoilrBundle\Service\GeoPosition;

abstract class BasePolicy implements AssignmentPolicyInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var \Boilr\BoilrBundle\Service\GeoDirectionInterface
     */
    protected $directionHelper;

    /**
     * @var Boilr\BoilrBundle\Policy\PolicyResult
     */
    protected $result;

    /**
     * Logger interface
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     *
     * @var \Boilr\BoilrBundle\Entity\User
     */
    protected $user;

    protected $installers = array();
    protected $interventions = array();

    public function __construct($entityManager, $directionHelper, $logger = null, $user = null)
    {
        $this->directionHelper = $directionHelper;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->user = $user;

        $this->result = new PolicyResult();
        $this->result->setPolicyName($this->getName());
        $this->result->setPolicyDescr($this->getDescription());
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        $assocs = $this->result->getAssociations();
        usort($assocs, array("\Boilr\BoilrBundle\Form\Model\InstallerForIntervention", "sortByScheduledDate"));
        $this->result->setAssociations($assocs);

        return $this->result;
    }

    public function setInterventions($interventions = array())
    {
        $this->interventions = $interventions;
    }

    public function setInstallers($installers = array())
    {
        $this->installers = $installers;
    }

    /**
     * Log message
     *
     * @param string $message
     */
    public function log($message)
    {
        if ($this->logger) {
            $this->logger->debug('[BOILR] ' . $message);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function apply(PolicyResult $result)
    {
        $success = true;
        $this->entityManager->beginTransaction();

        try {
            foreach ($result->getAssociations() as $assoc) {
                /* @var $assoc \Boilr\BoilrBundle\Form\Model\InstallerForIntervention */
                if ($assoc->getChecked() == true) {
                    $assoc->getIntervention()->setInstaller($assoc->getInstaller());
                }
            }
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exc) {
            $this->entityManager->rollback();
            $success = false;
        }

        return $success;
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
            /**
             * no interventions were found, consider company address as starting
             * point and far date (01/01/1970) as last intervention date
             */
            $where = new GeoPosition($installer->getCompany()->getLatitude(), $installer->getCompany()->getLongitude());
            $date = new \DateTime();
            $date->setDate(1970, 1, 1);

            $position = array('where' => $where, 'when' => $date);
        } else {
            // sort associations by date
            usort($associations, array("\Boilr\BoilrBundle\Policy\BasePolicy", "sortInterventionsByDate"));

            $lastAssoc = $associations[0];
            $address = $lastAssoc->getIntervention()->getFirstSystem()->getAddress();

            $where = new GeoPosition($address->getLatitude(), $address->getLongitude());
            $date = $lastAssoc->getIntervention()->getExpectedCloseDate();

            $position = array('where' => $where, 'when' => $date);
        }

        return $position;
    }

    /**
     * Returns zero if expected close dates are equals, -1/1 if the former is
     * smaller/greater then first
     *
     * @param  InstallerForIntervention $i1
     * @param  InstallerForIntervention $i2
     * @return int
     */
    public static function sortInterventionsByDate(InstallerForIntervention $i1, InstallerForIntervention $i2)
    {
        $date1 = $i1->getIntervention()->getExpectedCloseDate();
        $date2 = $i2->getIntervention()->getExpectedCloseDate();

        if ($date1 == $date2) {
            return 0;
        }

        return ($date1 < $date2) ? -1 : 1;
    }

}

<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\Template,
    Boilr\BoilrBundle\Form\Model\MaintenanceInterventionFilter,
    Boilr\BoilrBundle\Form\Model\InterventionDetailResults;
use Doctrine\ORM\EntityRepository;

/**
 * MaintenanceInterventionRepository
 */
class MaintenanceInterventionRepository extends EntityRepository
{

    /**
     * Returns list of manteinance interventions for given customer
     *
     * @param MyPerson $person
     * @return array
     */
    public function interventionsForCustomer(MyPerson $person)
    {
        $interventions = $this->getEntityManager()->createQueryBuilder()
                        ->select('mi')
                        ->from('BoilrBundle:MaintenanceIntervention', 'mi')
                        ->where('mi.customer = :owner')
                        ->orderBy('mi.scheduledDate')
                        ->setParameter('owner', $person)
                        ->getQuery()->getResult();

        return $interventions;
    }

    /**
     * Returns list of manteinance interventions linked to given installer
     *
     * @param Installer $installer
     * @return array
     */
    public function interventionsForInstaller(Installer $installer)
    {
        $interventions = $this->getEntityManager()->createQueryBuilder()
                        ->select('mi')
                        ->from('BoilrBundle:MaintenanceIntervention', 'mi')
                        ->where('mi.installer = :installer')
                        ->orderBy('mi.scheduledDate')
                        ->setParameter('installer', $installer)
                        ->getQuery()->getResult();

        return $interventions;
    }

    /**
     * Returns all interventions within a date interval
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    public function interventionsBetweenDates($start, $end)
    {
        $records = $this->getEntityManager()->createQuery(
                        "SELECT si FROM BoilrBundle:MaintenanceIntervention si " .
                        "WHERE si.scheduledDate >= :date1 AND si.scheduledDate <= :date2 " .
                        "ORDER BY si.scheduledDate")
                ->setParameters(array('date1' => $start, 'date2' => $end))
                ->getResult();

        return $records;
    }

    /**
     * Returns true if given intervention overlaps some other interventions
     *
     * @param \DateTime $aDate
     * @return boolean
     */
    public function doesInterventionOverlaps(MaintenanceIntervention $interv)
    {
        $aDate = $interv->getScheduledDate();
        $miCount = $this->getEntityManager()->createQuery(
                                "SELECT COUNT(mi) FROM BoilrBundle:MaintenanceIntervention mi " .
                                "WHERE :date >= mi.scheduledDate AND :date <= mi.expectedCloseDate")
                        ->setParameter('date', $aDate)->getSingleScalarResult();

        return ($miCount > 0);
    }

    /**
     * Evaluate expected close time based on operation group time length
     *
     * @param MaintenanceIntervention $interv
     */
    public function evalExpectedCloseDate(MaintenanceIntervention $interv)
    {
        if (!$interv->getScheduledDate()) {
            return;
        }

        $repo = $this->getEntityManager()->getRepository('BoilrBundle:OperationGroup');
        $aDate = new \DateTime();
        $aDate->setTimestamp($interv->getScheduledDate()->format('U'));

        // For each system belonging to this intervention, evaluate work time
        foreach ($interv->getDetails() as $detail) {
            /* @var $detail \Boilr\BoilrBundle\Entity\InterventionDetail */

            if ($detail->getChecked()) {
                $timeLength = $repo->getEstimatedTimeLength($detail->getOperationGroup());
                $interval = \DateInterval::createFromDateString("+" . $timeLength . " second");
                $aDate->add($interval);
            }
        }

        $interv->setExpectedCloseDate($aDate);
    }

    public function searchInterventions(MaintenanceInterventionFilter $filter)
    {
        $params = array();
        $qb = $this->getEntityManager()->createQueryBuilder()->select('mi')
                ->from('BoilrBundle:MaintenanceIntervention', 'mi')
                ->orderBy('mi.scheduledDate');

        // Date interval
        if ($filter->getSearchByDate()) {
            $qb->andWhere('mi.scheduledDate >= :date1 AND mi.scheduledDate <= :date2');
            $params += array('date1' => $filter->getStartDate(), 'date2' => $filter->getEndDate());
        }

        // Only planned?
        if ($filter->getPlanned()) {
            $qb->andWhere('mi.isPlanned = 1');
        }

        if ($filter->getWithoutInstaller()) {
            $qb->andWhere('mi.installer IS NULL');
        }

        // Status
        if (count($filter->getStatus()) > 0) {
            $qb->andWhere('mi.status in (:statuses)');
            $params += array('statuses' => $filter->getStatus());
        }

        if (count($params)) {
            $qb->setParameters($params);
        }

        return $qb->getQuery()->getResult();
    }

    public function persistUnplannedIntervention(MaintenanceIntervention $interv)
    {
        // verify that given intervention doesn't overlap with any other
        if ($this->doesInterventionOverlaps($interv)) {
            return array('success' => false, 'message' => "La data/ora richiesta si sovrappone con un altro appuntamento.");
        }

        // purge any unchecked system
        $detailsToRemove = array();
        foreach ($interv->getDetails() as $detail) {
            if (!$detail->getChecked()) {
                $detailsToRemove[] = $detail;
            }
        }
        foreach ($detailsToRemove as $detail) {
            $interv->getDetails()->removeElement($detail);
        }

        // set default installer, if any
        $details = $interv->getDetails();
        $defaultInstaller = $details[0]->getSystem()->getDefaultInstaller();
        if ($defaultInstaller) {
            $interv->setInstaller($defaultInstaller);
        }

        // evaluate close date based on system types
        $this->evalExpectedCloseDate($interv);

        $em = $this->getEntityManager();
        $em->beginTransaction();
        try {
            $em->persist($interv);
            $em->flush();
            $em->commit();
        } catch (Exception $exc) {
            $em->rollback();
            return array('success' => false, 'message' => 'Si è verificato un errore durante il salvataggio');
        }

        return array('success' => true);
    }

    public function persistCheckResults(InterventionDetailResults $model)
    {
        $result = true;
        $em = $this->getEntityManager();

        try {
            $em->beginTransaction();
            foreach ($model->getChecks() as $check) {
                $em->persist($check);
            }
            $em->flush();
            $this->markInterventionAsChecked($model->getInterventionDetail()->getIntervention());
            $em->commit();
        } catch (Exception $exc) {
            $em->rollback();
            $result = false;
        }

        return $result;
    }

    /**
     * If all systems in this intervention were checked and installed stored
     * inspection results, the intervention is marked as "hasCheckResults"
     *
     * @param MaintenanceIntervention $interv
     */
    public function markInterventionAsChecked(MaintenanceIntervention $interv)
    {
        $success = true;
        foreach ($interv->getDetails() as $detail) {
            if ($detail->getChecks()->count() == 0) {
                $success = false;
            }
        }

        if ($success) {
            $interv->setHasCheckResults(true);
            $this->getEntityManager()->flush();
        }
    }

    /**
     *
     * @param MaintenanceIntervention $interv
     * @param Template $template
     */
    public function prepareDocument(MaintenanceIntervention $interv, Template $template)
    {
        $document = array();

        // for each system (detail) in this manteinance intervention
        foreach ($interv->getDetails() as $interventionDetail) {
            $intChecks = $interventionDetail->getChecks()->getValues();
            $sections = array();

            // for each section of given template
            foreach ($template->getSections() as $templateSection) {
                $currentSection = array('sectionName' => $templateSection->getName(), 'sectionResults' => array());

                // for each operation in current template section
                foreach ($templateSection->getOperations() as $sectionOperation) {
                    $results = array_filter($intChecks, function ($entry) use ($sectionOperation) {
                                if ($entry->getName() == $sectionOperation->getParentOperation()->getName()) {
                                    return true;
                                }
                                return false;
                            });
                    if (count($results) == 1) {
                        $interventionCheck = array_pop($results);

                        $inspection = array(
                            'checkName' => $interventionCheck->getName(),
                            'resultType' => $interventionCheck->getResultType(),
                            'textValue' => $interventionCheck->getTextValue(),
                            'threewayValue' => $interventionCheck->getThreewayValue(),
                        );
                        $currentSection['sectionResults'][] = $inspection;
                    }
                }
                $sections[] = $currentSection;
            }
            $detail = array('system' => $interventionDetail->getSystem(), 'sections' => $sections);
            $document[] = $detail;
        }

        return $document;
    }

}
<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\Template,
    Boilr\BoilrBundle\Form\Model\ManteinanceInterventionFilter,
    Boilr\BoilrBundle\Form\Model\InterventionDetailResults;
use Doctrine\ORM\EntityRepository;

/**
 * ManteinanceInterventionRepository
 */
class ManteinanceInterventionRepository extends EntityRepository
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
                        ->from('BoilrBundle:ManteinanceIntervention', 'mi')
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
                        ->from('BoilrBundle:ManteinanceIntervention', 'mi')
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
                        "SELECT si FROM BoilrBundle:ManteinanceIntervention si " .
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
    public function doesInterventionOverlaps(ManteinanceIntervention $interv)
    {
        $aDate = $interv->getScheduledDate();
        $miCount = $this->getEntityManager()->createQuery(
                                "SELECT COUNT(mi) FROM BoilrBundle:ManteinanceIntervention mi " .
                                "WHERE :date >= mi.scheduledDate AND :date <= mi.expectedCloseDate")
                        ->setParameter('date', $aDate)->getSingleScalarResult();

        return ($miCount > 0);
    }

    /**
     * Evaluate expected close time based on operation group time length
     *
     * @param ManteinanceIntervention $interv
     */
    public function evalExpectedCloseDate(ManteinanceIntervention $interv)
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

    public function searchInterventions(ManteinanceInterventionFilter $filter)
    {
        $params = array();
        $qb = $this->getEntityManager()->createQueryBuilder()->select('mi')
                ->from('BoilrBundle:ManteinanceIntervention', 'mi')
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

    public function persistUnplannedIntervention(ManteinanceIntervention $interv)
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
     * @param ManteinanceIntervention $interv
     */
    public function markInterventionAsChecked(ManteinanceIntervention $interv)
    {
        $success = true;
        foreach ($interv->getDetails() as $detail) {
            if ($detail->getChecks()->getCount() == 0) {
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
     * @param ManteinanceIntervention $interv
     * @param Template $template
     */
    public function prepareDocument(ManteinanceIntervention $interv, Template $template)
    {
        $document = array();

        // for each system in this manteinance intervention
        foreach ($interv->getDetails() as $interventionDetail) {
            $sections = array();

            // for each section of given template
            foreach ($template->getSections() as $templateSection) {
                $currentSection = array('sectionName' => $templateSection->getName(), 'sectionResults' => array());

                // for each operation in current template section
                foreach ($templateSection->getOperations() as $sectionOperation) {

                    // find an InterventionCheck instance whose parentOperation is sectionOperation
                    $intChecks = $interventionDetail->getChecks()->getValues();
                    $results = array_filter($intChecks, function ($entry) use ($sectionOperation)
                                                        {
                                                            if ($entry->getParentOperation()->getId() === $sectionOperation->getId()) {
                                                                return true;
                                                            }
                                                            return false;
                                                        });
                    if (count($results) == 1) {
                        $interventionCheck = array_pop($results);
                        $textValue = $interventionCheck->getTextValue();
                        $threewayValue = $interventionCheck->getThreewayValue();

                        $inspection = array(
                            'checkName' => $sectionOperation->getName(),
                            'resultType' => $sectionOperation->getResultType(),
                            'textValue' => $textValue,
                            'threewayValue' => $threewayValue
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
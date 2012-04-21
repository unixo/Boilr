<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Form\Model\ManteinanceInterventionFilter;
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

}
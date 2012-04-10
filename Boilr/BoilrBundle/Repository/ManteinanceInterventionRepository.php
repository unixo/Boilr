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
                            "SELECT si FROM BoilrBundle:ManteinanceIntervention si ".
                            "WHERE si.scheduledDate >= :date1 AND si.scheduledDate <= :date2 ".
                            "ORDER BY si.scheduledDate")
                        ->setParameters(array('date1' => $start, 'date2' => $end))
                        ->getResult();

        return $records;
    }

    /**
     * Returns true if given intervention overlaps some other interventions
     *
     * @param ManteinanceIntervention $interv
     * @return boolean
     */
    public function doesInterventionOverlaps(ManteinanceIntervention $interv)
    {
        $aDate   = $interv->getOriginalDate();
        $miCount = $this->getEntityManager()->createQuery(
                "SELECT COUNT(mi) FROM BoilrBundle:ManteinanceIntervention mi ".
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
        if (! $interv->getOriginalDate() || ! $interv->getDefaultOperationGroup()) {
            return;
        }

        $repo       = $this->getEntityManager()->getRepository('BoilrBundle:OperationGroup');
        $timeLength = $repo->getEstimatedTimeLength( $interv->getDefaultOperationGroup() );
        $interval   = \DateInterval::createFromDateString("+". $timeLength ." second");

        $aDate = new \DateTime();
        $aDate->setTimestamp( $interv->getOriginalDate()->format('U') );
        $aDate->add($interval);

        $interv->setExpectedCloseDate($aDate);
    }

    public function searchInterventions(ManteinanceInterventionFilter $filter)
    {
        $params = array();
        $qb     = $this->getEntityManager()->createQueryBuilder()->select('mi')
                       ->from('BoilrBundle:ManteinanceIntervention', 'mi')
                       ->orderBy('mi.scheduledDate');

        // Date interval
        if ($filter->getSearchByDate()) {
            $qb->andWhere('mi.scheduledDate >= :date1 AND mi.originalDate <= :date2');
            $params += array('date1' => $filter->getStartDate(), 'date2' => $filter->getEndDate());
        }

        // Only planned?
        if ($filter->getPlanned()) {
            $qb->andWhere('mi.isPlanned = 1');
        }

        // Status
        if (count($filter->getStatus()) > 0) {
            $qb->andWhere('mi.status in (:statuses)');
            $params += array('statuses' => $filter->getStatus() );
        }

        if (count($params)) {
            $qb->setParameters($params);
        }

        return $qb->getQuery()->getResult();
    }
}
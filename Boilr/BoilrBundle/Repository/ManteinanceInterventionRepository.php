<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\OperationGroup;

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
                              ->orderBy('mi.originalDate')
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
                            "WHERE si.originalDate >= :date1 AND si.originalDate <= :date2 ".
                            "ORDER BY si.originalDate")
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
                "WHERE :date >= mi.originalDate AND :date <= mi.expectedCloseDate")
                        ->setParameter('date', $aDate)->getSingleScalarResult();

        return ($miCount > 0);
    }

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
}
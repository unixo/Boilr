<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Doctrine\ORM\EntityRepository;

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
}
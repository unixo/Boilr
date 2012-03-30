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
}
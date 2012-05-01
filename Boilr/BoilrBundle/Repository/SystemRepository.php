<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\System as MySystem,
    Doctrine\ORM\EntityRepository;

/**
 * SystemRepository
 */
class SystemRepository extends EntityRepository
{
    public function hasSystemDocsOrInterventions(MySystem $system)
    {
        $count = $this->getEntityManager()->createQuery(
                "SELECT COUNT(a) FROM BoilrBundle:Attachment a WHERE a.parentSystem = :sys"
                )->setParameter('sys', $system)->getSingleScalarResult();
        if ($count > 0) {
            return true;
        }

        $count = $this->getEntityManager()->createQuery(
                "SELECT COUNT(id) FROM BoilrBundle:InterventionDetail mi WHERE a.system = :sys"
                )->setParameter('sys', $system)->getSingleScalarResult();
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * Delete an instance of system. Returns true if successful
     *
     * @param MySystem $system
     * @return boolean
     */
    public function deleteSystem(MySystem $system)
    {
        $success = true;
        $em      = $this->getEntityManager();

        try {
            $em->beginTransaction();
            $person  = $system->getOwner();
            $person->getSystems()->removeElement($system);
            $em->remove($system);
            $em->flush();
            $em->commit();
        } catch (\PDOException $exc) {
            $em->rollback();
            $success = false;
        }

        return $success;
    }
}
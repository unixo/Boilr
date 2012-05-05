<?php

namespace Boilr\BoilrBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\Installer;

/**
 * InstallerRepository
 */
class InstallerRepository extends EntityRepository
{

    public function getLoadForInstaller(Installer $installer)
    {
        $status = array(ManteinanceIntervention::STATUS_ABORTED, ManteinanceIntervention::STATUS_CLOSED);
        $count = $this->getEntityManager()->createQuery(
                        "SELECT COUNT(mi) FROM BoilrBundle:ManteinanceIntervention mi " .
                        "WHERE mi.installer = :inst AND mi.status NOT IN (:status)"
                )
                ->setParameters(array('inst' => $installer, 'status' => $status))
                ->getSingleScalarResult();

        return $count;
    }

}
<?php

namespace Boilr\BoilrBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Entity\Installer;

/**
 * InstallerRepository
 */
class InstallerRepository extends EntityRepository
{

    public function getLoadForInstaller(Installer $installer)
    {
        $status = array(MaintenanceIntervention::STATUS_ABORTED, MaintenanceIntervention::STATUS_CLOSED);
        $count = $this->getEntityManager()->createQuery(
                        "SELECT COUNT(mi) FROM BoilrBundle:MaintenanceIntervention mi " .
                        "WHERE mi.installer = :inst AND mi.status NOT IN (:status)"
                )
                ->setParameters(array('inst' => $installer, 'status' => $status))
                ->getSingleScalarResult();

        return $count;
    }

}
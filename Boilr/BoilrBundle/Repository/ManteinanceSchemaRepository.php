<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\ManteinanceSchema;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query;


class ManteinanceSchemaRepository extends EntityRepository
{
    public function persistSchema(ManteinanceSchema $schema)
    {
        $success = true;
        $em      = $this->getEntityManager();

        try {
            if ($schema->getId() == NULL) {
                $order = $em->createQuery(
                                    "SELECT MAX(s.listOrder) ".
                                    "FROM BoilrBundle:ManteinanceSchema s ".
                                    "WHERE s.systemType = :sysType")
                            ->setParameter('sysType', $schema->getSystemType())
                            ->getSingleScalarResult();
                $schema->setListOrder($order++);
                $em->persist($schema);
            }
            $em->flush();
        } catch (PDOException $exc) {
            $success = false;
        }

        return $success;
    }
}
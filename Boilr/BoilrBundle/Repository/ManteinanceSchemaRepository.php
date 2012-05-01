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
        $em = $this->getEntityManager();
        $count = $schema->getSystemType()->getSchemas()->count();

        $em->persist($schema);
        try {
            $schema->setListOrder($count);
            if ($schema->getId() === null) {
                $em->persist($schema);
            }
            $em->flush();
        } catch (PDOException $exc) {
            $success = false;
        }

        return $success;
    }

}
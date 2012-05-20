<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Doctrine\ORM\EntityRepository;

/**
 * PersonRepository
 */
class PersonRepository extends EntityRepository
{
    /**
     * Persist an instance of Person class
     *
     * @param MyPerson $person
     * @return boolean
     */
    public function persistPerson(MyPerson $person)
    {
        $success = true;
        $em      = $this->getEntityManager();

        try {
            $em->beginTransaction();
            $em->persist($person);

            foreach ($person->getAddresses() as $address) {
                $address->setPerson($person);
                $em->persist($address);
            }
            foreach ($person->getSystems() as $system) {
                $system->setOwner($person);
                $em->persist($system);
            }

            $em->flush();
            $em->commit();
        } catch (\PDOException $exc) {
            $em->rollback();
            $success = false;
        }

        return $success;
    }
}
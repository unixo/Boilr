<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Boilr\BoilrBundle\Entity\OperationGroup;

class OperationGroupData extends AbstractFixture implements OrderedFixtureInterface
{

    function load(ObjectManager $manager)
    {
        $og1 = new OperationGroup();
        $og1->setName("OPS1");
        $og1->setDescr("Gruppo controlli di base");
        $manager->persist($og1);

        $og2 = new OperationGroup();
        $og2->setName("OPS2");
        $og2->setDescr("Gruppo controlli per bollino");
        $manager->persist($og2);

        $manager->flush();
        $this->addReference('ops1', $og1);
        $this->addReference('ops2', $og2);
    }

    function getOrder()
    {
        return 200;
    }

}
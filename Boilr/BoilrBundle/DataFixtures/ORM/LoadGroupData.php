<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Group;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $g = new Group();
        $g->setName('Operatore');
        $g->setRole('ROLE_OPERATOR');
        $manager->persist($g);
        $manager->flush();
        $this->addReference('group-operator', $g);
        
        $g = new Group();
        $g->setName('Amministratore');
        $g->setRole('ROLE_ADMIN');
        $manager->persist($g);
        $manager->flush();
        $this->addReference('group-admin', $g);
        
        $g = new Group();
        $g->setName('Super-User');
        $g->setRole('ROLE_SUPERUSER');
        $manager->persist($g);
        $manager->flush();
        $this->addReference('group-super', $g);
    }

    function getOrder()
    {
        return 30;
    }
}
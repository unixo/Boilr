<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Config as MyConfig;

class LoadConfigData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $c = new MyConfig();
        $c->setSetting(MyConfig::KEY_WORKDAY_START);
        $c->setValue("08:00");
        $manager->persist($c);

        $c = new MyConfig();
        $c->setSetting(MyConfig::KEY_WORKDAY_END);
        $c->setValue("17:00");
        $manager->persist($c);

        $manager->flush();
    }

    function getOrder()
    {
        return 300;
    }
}
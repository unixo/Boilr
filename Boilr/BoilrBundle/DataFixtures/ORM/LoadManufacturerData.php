<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Manufacturer;

class LoadManufacturerData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $m = new Manufacturer();
        $m->setName('Ariston thermo');        
        $manager->persist($m);
        $manager->flush();
        $this->addReference('manufacturer-ariston', $m);
        
        $m = new Manufacturer();
        $m->setName('Atag');
        $manager->persist($m);
        $manager->flush();
        $this->addReference('manufacturer-atag', $m);
        
        $m = new Manufacturer();
        $m->setName('Riello');
        $manager->persist($m);
        $manager->flush();
        $this->addReference('manufacturer-riello', $m);
        
        $m = new Manufacturer();
        $m->setName('Bosh');
        $manager->persist($m);
        $manager->flush();        
    }

    public function getOrder()
    {
        return 1;
    }
}
<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\SystemType;

class LoadSystemTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $s = new SystemType();
        $s->setName('Impianto riscaldamento autonomo');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-riscald-autonomo', $s);

        $s = new SystemType();
        $s->setName('Centrale termina');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-centrale', $s);

        $s = new SystemType();
        $s->setName('Altro riscaldamento');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-altro-riscald', $s);

        $s = new SystemType();
        $s->setName('Climatizzatore monoblocco');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-clima-mono', $s);

        $s = new SystemType();
        $s->setName('Impianto climatizzazione');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-clima', $s);
    }

    public function getOrder()
    {
        return 2;
    }
}
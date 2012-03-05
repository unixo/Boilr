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
        $s->setName('Impianti termici con combustibile liquido');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-termico-comb-liquido', $s);

        $s = new SystemType();
        $s->setName('Impianti termici con combustibile solido');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-termico-comb-solido', $s);

        $s = new SystemType();
        $s->setName('Impianti termici con combustibile liquido/solido > 116kw');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-termico-comb-gt-116', $s);

        $s = new SystemType();
        $s->setName('Impianti a gas');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-gas', $s);

        $s = new SystemType();
        $s->setName('Impianti a gas > 350kw');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-gas-gt-350', $s);

        $s = new SystemType();
        $s->setName('Impianti a gas <= 35kw (tipo B)');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-gas-lt-35-B', $s);

        $s = new SystemType();
        $s->setName('Impianti a gas <= 35kw');
        $manager->persist($s);
        $manager->flush();
        $this->addReference('systemtype-gas-lt-35', $s);
    }

    public function getOrder()
    {
        return 2;
    }
}
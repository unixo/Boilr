<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\MaintenanceSchema,
    Boilr\BoilrBundle\Entity\OperationGroup;

class MaintenanceSchemaData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $ops1 = $manager->merge($this->getReference('ops1'));
        $ops2 = $manager->merge($this->getReference('ops2'));

        // Impianti termici con combustibile liquido: ogni 12 mesi, OPS1
        $st = $manager->merge($this->getReference('systemtype-termico-comb-liquido'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        // Impianti termici con combustibile solido: ogni 12 mesi, OPS1
        $st = $manager->merge($this->getReference('systemtype-termico-comb-solido'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        /**
         * Impianti termici con combustibile liquido/solido > 116kw:
         * - 3 mesi dopo la prima accensione
         * - in seguito ogni 12 mesi dalla data di prima accensione, OPS1
         */
        $st = $manager->merge($this->getReference('systemtype-termico-comb-gt-116'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(false);
        $ms->setFreq("3 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(1);
        $manager->persist($ms);

        // Impianti a gas > 35kw: ogni 12 mesi, OPS1
        $st = $manager->merge($this->getReference('systemtype-gas'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        /**
         * Impianti a gas > 350kw: ogni 12 mesi, OPS1
         * - 3 mesi dopo la prima accensione
         * - in seguito ogni 12 mesi dalla data di prima accensione, OPS1
         */
        $st = $manager->merge($this->getReference('systemtype-gas-gt-350'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(false);
        $ms->setFreq("3 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(1);
        $manager->persist($ms);

        // Impianti a gas <= 35kw, tipologia B: ogni 12 mesi, OPS1
        $st = $manager->merge($this->getReference('systemtype-gas-lt-35-B'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops1);
        $ms->setIsPeriodic(true);
        $ms->setFreq("12 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        // Impianti a gas <= 35kw: ogni 24 mesi, OPS1 + OPS2
        $st = $manager->merge($this->getReference('systemtype-gas-lt-35'));
        $ms = new MaintenanceSchema();
        $ms->setSystemType($st);
        $ms->setOperationGroup($ops2);
        $ms->setIsPeriodic(true);
        $ms->setFreq("24 month");
        $ms->setListOrder(0);
        $manager->persist($ms);

        $manager->flush();
    }

    function getOrder()
    {
        return 250;
    }
}
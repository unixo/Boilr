<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\ContractType;

class LoadContractTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $ct = new ContractType();
        $ct->setName('Contratto annuale ordinario');
        $ct->setPeriodicity(12);
        $manager->persist($ct);
        $manager->flush();
        $this->addReference('contracttype-ord-year', $ct);

        $ct = new ContractType();
        $ct->setName('Contratto annuale VIP');
        $ct->setPeriodicity(1);
        $manager->persist($ct);
        $manager->flush();
        $this->addReference('contracttype-vip-year', $ct);
        
        $ct = new ContractType();
        $ct->setName('Contratto semestrale ordinario');
        $ct->setPeriodicity(6);
        $manager->persist($ct);
        $manager->flush();        
    }

    public function getOrder()
    {
        return 40;
    }
}
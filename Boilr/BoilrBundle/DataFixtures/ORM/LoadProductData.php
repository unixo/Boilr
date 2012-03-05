<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Product;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $p = new Product();
        $p->setManufacturer( $manager->merge($this->getReference('manufacturer-ariston')) );
        $p->setName('Genus');
        $manager->persist($p);
        $manager->flush();
        $this->addReference('product-genius', $p);

        $p = new Product();
        $p->setManufacturer( $manager->merge($this->getReference('manufacturer-ariston')) );
        $p->setName('Genus Premium HP 45-65 Kw');
        $manager->persist($p);
        $manager->flush();

        $p = new Product();
        $p->setManufacturer( $manager->merge($this->getReference('manufacturer-atag')) );
        $p->setName('ATAG Q');
        $manager->persist($p);
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Template;

class TemplateData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $t = new Template();
        $t->setName('Allegato G');
        $t->setDescr("Rapporto di controllo tecnico per impianto termico");
        $manager->persist($t);
        $manager->flush();
        $this->addReference('template-g', $t);
    }

    function getOrder()
    {
        return 201;
    }
}
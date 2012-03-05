<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\TemplateSection,
    Boilr\BoilrBundle\Entity\TemplateItem;

class TemplateSectionData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $ops1      = $manager->merge($this->getReference('ops1'));
        $templateG = $manager->merge($this->getReference('template-g'));

        $s0 = new TemplateSection();
        $s0->setTemplate($templateG);
        $s0->setName("A. Identificazio dell'impianto");
        $s0->setListOrder(0);
        $s0->setTimeLength(600);
        $s0->setGroup($ops1);
        $manager->persist($s0);

        $s1 = new TemplateSection();
        $s1->setTemplate($templateG);
        $s1->setName("B. Documentazione tecnica di corredo");
        $s1->setListOrder(1);
        $s1->setTimeLength(500);
        $s1->setGroup($ops1);
        $manager->persist($s1);

        $s2 = new TemplateSection();
        $s2->setTemplate($templateG);
        $s2->setName("C. Esame visivo del locale d'installazione");
        $s2->setListOrder(2);
        $s2->setTimeLength(180);
        $s2->setGroup($ops1);
        $manager->persist($s2);

        $s3 = new TemplateSection();
        $s3->setTemplate($templateG);
        $s3->setName("D. Esame visivo dei canali da fumo");
        $s3->setListOrder(3);
        $s3->setTimeLength(300);
        $s3->setGroup($ops1);
        $manager->persist($s3);

        $manager->flush();
        $this->addReference('ops1-sectA', $s0);
        $this->addReference('ops1-sectB', $s1);
        $this->addReference('ops1-sectC', $s2);
        $this->addReference('ops1-sectD', $s3);
    }

    function getOrder()
    {
        return 202;
    }
}
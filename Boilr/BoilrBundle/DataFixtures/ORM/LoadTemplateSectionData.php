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

        $s4 = new TemplateSection();
        $s4->setTemplate($templateG);
        $s4->setName("E. Controllo evacuazione dei prodotti della combustione");
        $s4->setListOrder(4);
        $s4->setTimeLength(300);
        $s4->setGroup($ops1);
        $manager->persist($s4);

        $s5 = new TemplateSection();
        $s5->setTemplate($templateG);
        $s5->setName("F. Controllo dell'apparecchio");
        $s5->setListOrder(5);
        $s5->setTimeLength(300);
        $s5->setGroup($ops1);
        $manager->persist($s5);

        $s6 = new TemplateSection();
        $s6->setTemplate($templateG);
        $s6->setName("G. Controllo dell'impianto");
        $s6->setListOrder(6);
        $s6->setTimeLength(300);
        $s6->setGroup($ops1);
        $manager->persist($s6);

        $s7 = new TemplateSection();
        $s7->setTemplate($templateG);
        $s7->setName("H. Controllo del rendimento di combustione");
        $s7->setListOrder(7);
        $s7->setTimeLength(300);
        $s7->setGroup($ops1);
        $manager->persist($s7);

        $manager->flush();
        $this->addReference('ops1-sectA', $s0);
        $this->addReference('ops1-sectB', $s1);
        $this->addReference('ops1-sectC', $s2);
        $this->addReference('ops1-sectD', $s3);
        $this->addReference('ops1-sectE', $s4);
        $this->addReference('ops1-sectF', $s5);
        $this->addReference('ops1-sectG', $s6);
        $this->addReference('ops1-sectH', $s7);

    }

    function getOrder()
    {
        return 202;
    }
}
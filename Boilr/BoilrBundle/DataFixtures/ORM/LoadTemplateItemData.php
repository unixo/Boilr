<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\TemplateItem;

class TemplateItemData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        // OPS1 - Section A
        $s0 = $manager->merge($this->getReference('ops1-sectA'));

        $item = new TemplateItem();
        $item->setSection($s0);
        $item->setName("Dichiar. di conformità dell'impianto");
        $item->setTimeLength(60);
        $item->setListOrder(0);
        $manager->persist($item);

        // OPS1 - Section B
        $s1 = $manager->merge($this->getReference('ops1-sectB'));

        $item = new TemplateItem();
        $item->setSection($s1);
        $item->setName("Dichiar. di conformità dell'impianto");
        $item->setTimeLength(60);
        $item->setListOrder(0);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s1);
        $item->setName("Libretto di impianto");
        $item->setTimeLength(60);
        $item->setListOrder(1);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s1);
        $item->setName("Libretto d'uso e manutenzione");
        $item->setTimeLength(60);
        $item->setListOrder(2);
        $manager->persist($item);

        // OPS1 - Section C
        $s2 = $manager->merge($this->getReference('ops1-sectC'));

        $item = new TemplateItem();
        $item->setSection($s2);
        $item->setName("Idoneità del locale di installazione");
        $item->setTimeLength(60);
        $item->setListOrder(0);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s2);
        $item->setName("Adeguate dimensioni aperture ventilazione");
        $item->setTimeLength(60);
        $item->setListOrder(1);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s2);
        $item->setName("Aperture di ventilazione libere da ostruzioni");
        $item->setTimeLength(60);
        $item->setListOrder(2);
        $manager->persist($item);

        // OPS1 - Section D
        $s3 = $manager->merge($this->getReference('ops1-sectD'));

        $item = new TemplateItem();
        $item->setSection($s3);
        $item->setName("Pendenza corretta");
        $item->setTimeLength(60);
        $item->setListOrder(0);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s3);
        $item->setName("Sezioni corrette");
        $item->setTimeLength(60);
        $item->setListOrder(1);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s3);
        $item->setName("Curve corrette");
        $item->setTimeLength(60);
        $item->setListOrder(2);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s3);
        $item->setName("Lunghezza corretta");
        $item->setTimeLength(60);
        $item->setListOrder(3);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s3);
        $item->setName("Buono stato di conservazione");
        $item->setTimeLength(60);
        $item->setListOrder(4);
        $manager->persist($item);

        // OPS1 - Section E
        $s4 = $manager->merge($this->getReference('ops1-sectE'));

        $item = new TemplateItem();
        $item->setSection($s4);
        $item->setName("Scarico in camino singolo");
        $item->setTimeLength(60);
        $item->setListOrder(0);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s4);
        $item->setName("Scarico in canna fumaria collettiva ramificata");
        $item->setTimeLength(60);
        $item->setListOrder(1);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s4);
        $item->setName("Scarico a parete");
        $item->setTimeLength(60);
        $item->setListOrder(2);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s4);
        $item->setName("Per apparecchio a tiraggio naturale: non esistono riflussi dei fumi nel locale");
        $item->setTimeLength(60);
        $item->setListOrder(3);
        $manager->persist($item);

        $item = new TemplateItem();
        $item->setSection($s4);
        $item->setName("Per apparecchio a tiraggio forzato: assenza di perdite dai condotti di scarico");
        $item->setTimeLength(60);
        $item->setListOrder(4);
        $manager->persist($item);

        $manager->flush();
    }

    function getOrder()
    {
        return 203;
    }
}
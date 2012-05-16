<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Template,
    Boilr\BoilrBundle\Entity\TemplateSection,
    Boilr\BoilrBundle\Entity\Operation,
    Boilr\BoilrBundle\Entity\SectionOperation;

class TemplateData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        $filename = __DIR__.'/../SampleData/templates.csv';
        $fhandle  = fopen($filename, "r");

        $currentTemplate = null;
        $currentSection  = null;

        while ( ($record = fgetcsv($fhandle, 1000, '|')) !== FALSE ) {
            switch ($record[0]) {
            case 'T':
                $currentTemplate = new Template();
                $currentTemplate->setName($record[1]);
                $currentTemplate->setDescr($record[2]);
                $manager->persist($currentTemplate);
                break;

            case 'S':
                $currentSection = new TemplateSection();
                $currentSection->setTemplate($currentTemplate);
                $currentSection->setName($record[1]);
                $currentSection->setListOrder($record[2]);
                $currentTemplate->addTemplateSection($currentSection);
                $manager->persist($currentSection);
                break;

            case 'O':
                $parentOp = $manager->merge($this->getReference($record[1]));

                $sectionOp = new SectionOperation();
                $sectionOp->setParentSection($currentSection);
                $sectionOp->setParentOperation($parentOp);
                $sectionOp->setListOrder($record[2]);
                $currentSection->addSectionOperation($sectionOp);
                $manager->persist($sectionOp);
                break;
            }
        }
        fclose($fhandle);

        $manager->flush();
    }

    function getOrder()
    {
        return 202;
    }
}
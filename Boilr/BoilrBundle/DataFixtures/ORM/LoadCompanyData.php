<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Company,
    Boilr\BoilrBundle\Entity\Person as MyPerson;

class LoadCompanyData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        // Company data
        $c = new Company();
        $c->setName("Club 'Amici della caldaia'");
        $c->setVatCode('01234567890');
        $c->setStreet("Via delle Botteghe Oscure 15");
        $c->setCity("Roma");
        $c->setState("IT");
        $c->setPostalCode("00100");
        $c->setProvince("RM");

        $manager->persist($c);

        $filename   = __DIR__.'/../SampleData/installers.csv';
        $fhandle    = fopen($filename, "r");

        while ( ($record = fgetcsv($fhandle, 1000, '|')) !== FALSE ) {
            // name|surname|email|homePhone|fax
            if (count($record) < 5) {
                continue;
            }

            $p = new MyPerson();
            $p->setType(MyPerson::TYPE_PHYSICAL);
            $p->setCompany($c);
            $p->setName($record[0]);
            $p->setSurname($record[1]);
            $p->setPrimaryMail($record[2]);
            $p->setHomePhone($record[3]);
            $p->setFaxNumber($record[4]);
            $p->setIsCustomer(false);
            $p->setIsInstaller(true);
            $p->setIsAdministrator(false);

            $manager->persist($p);
        }
        fclose($fhandle);

        $manager->flush();
    }

    function getOrder()
    {
        return 300;
    }
}
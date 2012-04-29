<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Address as MyAddress;

class LoadPersonData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $filename = __DIR__ . '/../SampleData/random_people.csv';
        $fhandle = fopen($filename, "r");
        $count = 0;
        $skipped = 0;
        $lastPerson = null;

        while (($record = fgetcsv($fhandle, 1000, '|')) !== FALSE) {
            // name|surname|email|homePhone|fax|street|city|postalCode|prov|state
            if (count($record) < 9) {
                $skipped++;
                continue;
            }

            if ($record[0] !== '-') {
                $p = new MyPerson();
                $p->setType(MyPerson::TYPE_PHYSICAL);
                $p->setName($record[0]);
                $p->setSurname($record[1]);
                $p->setPrimaryMail($record[2]);
                $p->setHomePhone($record[3]);
                $p->setFaxNumber($record[4]);
                $p->setIsAdministrator((rand() % 2));
            } else {
                $p = $lastPerson;
            }

            $a = new MyAddress();
            $a->setType((rand() % 2) ? MyAddress::TYPE_HOME : MyAddress::TYPE_OFFICE );
            $a->setStreet($record[5]);
            $a->setCity($record[6]);
            $a->setPostalCode($record[7]);
            $a->setProvince($record[8]);
            $a->setState($record[9]);
            $a->setPerson($p);

            $p->addAddress($a);

            $manager->persist($p);
            $count++;

            if ($count % 100) {
                $manager->flush();
            }

            $lastPerson = $p;
        }
        fclose($fhandle);

        $manager->persist($p);
        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }

}
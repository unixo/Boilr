<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    \Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Boilr\BoilrBundle\Entity\Company,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\Person as MyPerson;

class LoadCompanyData extends AbstractFixture implements OrderedFixtureInterface
{
    function load(ObjectManager $manager)
    {
        // Abilities
        $st1 = $manager->merge($this->getReference('systemtype-termico-comb-liquido'));
        $st2 = $manager->merge($this->getReference('systemtype-termico-comb-solido'));
        $st3 = $manager->merge($this->getReference('systemtype-termico-comb-gt-116'));
        $st4 = $manager->merge($this->getReference('systemtype-gas'));
        $st5 = $manager->merge($this->getReference('systemtype-gas-gt-350'));
        $st6 = $manager->merge($this->getReference('systemtype-gas-lt-35-B'));
        $st7 = $manager->merge($this->getReference('systemtype-gas-lt-35'));

        // Company data
        $c = new Company();
        $c->setName("Assistenza Caldaie Roma");
        $c->setOfficePhone('06.9102907');
        $c->setCellularPhone('331.3588218 ');
        $c->setVatCode('01234567890');
        $c->setStreet("Via di Monte Verde, 53");
        $c->setCity("Roma");
        $c->setState("IT");
        $c->setPostalCode("00100");
        $c->setProvince("RM");

        $manager->persist($c);

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

            $i = new Installer();
            $i->setCompany($c);
            $i->setName($record[0]);
            $i->setSurname($record[1]);
            $i->setEmail($record[2]);
            $i->setOfficePhone($record[3]);
            $c->addInstaller($i);

            $index = rand()%3;
            $abilities = null;
            if ($index == 0) {
                $abilities = array($st1, $st2, $st3);
            } else if ($index == 1) {
                $abilities = array($st4, $st5, $st6);
            } else {
                $abilities = array($st2, $st4, $st7);
            }
            foreach ($abilities as $ab) {
                $i->addSystemType($ab);
            }

            $manager->persist($i);
        }
        fclose($fhandle);

        $manager->flush();
    }

    function getOrder()
    {
        return 300;
    }
}
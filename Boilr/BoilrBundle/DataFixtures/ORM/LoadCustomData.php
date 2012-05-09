<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Address as MyAddress,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Entity\InterventionDetail,
    Boilr\BoilrBundle\Entity\InterventionCheck;

class LoadCustomData extends AbstractFixture implements OrderedFixtureInterface
{

    function load(ObjectManager $manager)
    {
        return;

        // Installer address #1
        $addr0 = new MyAddress();
        $addr0->setStreet('Via Cristoforo Colombo 123');
        $addr0->setPostalCode('00100');
        $addr0->setCity('Roma');
        $addr0->setState('Italia');
        $addr0->setProvince('Roma');
        $addr0->setType(MyAddress::TYPE_OFFICE);

        // Installer #1
        $inst1 = new Installer();
        $inst1->setName("Tore");
        $inst1->setSurname("Installa");
        $inst1->setOfficePhone('06 123456');
        $addr0->setPerson($inst1);

        $manager->persist($inst1);

        //---------------------------------------------------------------------
        // Customer address #1
        $addr1 = new MyAddress();
        $addr1->setStreet('Via Sagittario 17');
        $addr1->setPostalCode('00040');
        $addr1->setCity('Pomezia');
        $addr1->setState('Italia');
        $addr1->setProvince('Roma');
        $addr1->setType(MyAddress::TYPE_HOME);

        // System
        $aDate1 = new \DateTime();
        $aDate1->setDate(2010, 4, 7);
        $aDate2 = new \DateTime();
        $aDate2->setDate(2011, 5, 8);

        $sys1 = new MySystem();
        $sys1->setCode("FE07041975-1");
        $sys1->setDescr('Caldaia a gas');
        $sys1->setInstallDate($aDate1);
        $sys1->setLastMaintenance($aDate2);
        $sys1->setAddress($addr1);
        $sys1->setSystemType($manager->merge($this->getReference('systemtype-gas-lt-35')));
        $sys1->setProduct($manager->merge($this->getReference('product-genius')));
        $sys1->setDefaultInstaller($inst1);

        // Customer #1
        $cust1 = new MyPerson();
        $cust1->setType(MyPerson::TYPE_PHYSICAL);
        $cust1->setName("Pinco");
        $cust1->setSurname("Pallo");
        $cust1->setIsCustomer(true);
        $cust1->setCellularPhone('335 41 14 444');
        $cust1->setAddresses(array($addr1));
        $addr1->setPerson($cust1);
        $cust1->addSystem($sys1);
        $sys1->setOwner($cust1);

        // unplanned intervention
        $aDate1->setDate(2011, 8, 10);
        $aDate1->setTime(10, 0, 0);
        $aDate2->setDate(2011, 8, 10);
        $aDate2->setTime(10, 50, 0);
        $mi = MaintenanceIntervention::UnplannedInterventionFactory();
        $mi->setScheduledDate($aDate1);
        $mi->setExpectedCloseDate($aDate2);
        $mi->setCustomer($cust1);
        $mi->setInstaller($inst1);
        $mi->setStatus(MaintenanceIntervention::STATUS_CLOSED);

        $detail = new InterventionDetail();
        $detail->setIntervention($mi);
        $detail->setSystem($sys1);
        $detail->setOperationGroup($manager->merge($this->getReference('ops1')));
        $mi->addInterventionDetail($detail);

        $manager->persist($cust1);
        $manager->persist($mi);
        $manager->flush();
    }

    function getOrder()
    {
        return 1000;
    }

}
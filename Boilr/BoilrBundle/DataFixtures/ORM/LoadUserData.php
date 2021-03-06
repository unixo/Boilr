<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Boilr\BoilrBundle\Entity\User as MyUser;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $company = $manager->merge($this->getReference('company-main'));

        $u = new MyUser();
        $u->setName('Ferruccio');
        $u->setSurname('Vitale');
        $u->setLogin('unixo');
        $u->setPassword('');
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-super')));
        $u->setCompany($company);
        $manager->persist($u);
        $manager->flush();

        $u = new MyUser();
        $u->setName('Maurizio');
        $u->setSurname('Maffi');
        $u->setLogin('mm');
        $u->setPassword(''); // m4ur1z10
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-admin')));
        $u->setCompany($company);
        $manager->persist($u);
        $manager->flush();

        $u = new MyUser();
        $u->setName('Operatore');
        $u->setSurname('Fittizio');
        $u->setLogin('operator');
        $u->setPassword('fe96dd39756ac41b74283a9292652d366d73931f'); // operator
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-operator')));
        $u->setCompany($company);
        $manager->persist($u);
        $manager->flush();
    }

    public function getOrder()
    {
        return 302;
    }

}
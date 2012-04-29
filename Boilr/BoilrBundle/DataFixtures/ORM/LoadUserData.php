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
        $u = new MyUser();
        $u->setName('Ferruccio');
        $u->setSurname('Vitale');
        $u->setLogin('unixo');
        $u->setPassword('4853eb41f1c4ced4cdcb670c485580c1c510389b');
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-super')));
        $manager->persist($u);
        $manager->flush();

        $u = new MyUser();
        $u->setName('Maurizio');
        $u->setSurname('Maffi');
        $u->setLogin('mm');
        $u->setPassword('42bb6a44f833e29601aff89757b05f9adaed617c'); // m4ur1z10
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-admin')));
        $manager->persist($u);
        $manager->flush();

        $u = new MyUser();
        $u->setName('Operatore');
        $u->setSurname('Fittizio');
        $u->setLogin('operator');
        $u->setPassword('fe96dd39756ac41b74283a9292652d366d73931f'); // operator
        $u->setIsActive(true);
        $u->addGroup($manager->merge($this->getReference('group-operator')));
        $manager->persist($u);
        $manager->flush();
    }

    public function getOrder()
    {
        return 31;
    }

}
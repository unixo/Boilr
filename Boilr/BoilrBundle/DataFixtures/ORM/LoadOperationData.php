<?php

namespace Boilr\BoilrBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\Persistence\ObjectManager,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Boilr\BoilrBundle\Entity\Operation;

/**
 * Description of LoadOperationData
 *
 * @author unixo
 */
class LoadOperationData extends AbstractFixture implements OrderedFixtureInterface
{

    function load(ObjectManager $manager)
    {
        $filename = __DIR__ . '/../SampleData/operations.csv';
        $fhandle = fopen($filename, "r");

        while (($record = fgetcsv($fhandle, 1000, '|')) !== FALSE) {
            // group|name|time_length|order|placeholder
            $op = new Operation();
            $op->setParentGroup($manager->merge($this->getReference($record[0])));
            $op->setName($record[1]);
            $op->setTimeLength($record[2]);
            $op->setListOrder($record[3]);
            $op->setResultType(Operation::RESULT_CHECKBOX);
            $manager->persist($op);
            $this->addReference($record[4], $op);
        }
        fclose($fhandle);

        $manager->flush();
    }

    function getOrder()
    {
        return 201;
    }

}
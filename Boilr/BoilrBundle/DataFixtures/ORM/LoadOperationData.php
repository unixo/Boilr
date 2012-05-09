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

        $ops1 = $manager->merge($this->getReference("ops1"));
        $ops2 = $manager->merge($this->getReference("ops2"));

        while (($record = fgetcsv($fhandle, 1000, '|')) !== FALSE) {
            // group|name
            $op = new Operation();
            switch ($record[0]) {
                case "ops1":
                    $ops1->addOperation($op);
                    $op->addOperationGroup($ops1);
                    break;
                case "ops2":
                    $ops2->addOperation($op);
                    $op->addOperationGroup($ops2);
                    break;
                case "ops1+2":
                    $ops1->addOperation($op);
                    $ops2->addOperation($op);
                    $op->addOperationGroup($ops1);
                    $op->addOperationGroup($ops2);
                    break;
            }
            $op->setName($record[1]);
            $op->setResultType(Operation::RESULT_CHECKBOX);
            $manager->persist($op);
            $this->addReference($record[2], $op);
        }
        fclose($fhandle);

        $manager->flush();
    }

    function getOrder()
    {
        return 201;
    }

}
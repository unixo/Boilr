<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Contract as MyContract,
    Boilr\BoilrBundle\Entity\SystemType as MySystemType,
    Boilr\BoilrBundle\Entity\ManteinanceSchema as MyManteinanceSchema,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Doctrine\ORM\EntityRepository;

/**
 * ContractRepository
 */
class ContractRepository extends EntityRepository
{

    public function createNewContract(MyContract $contract)
    {
        $success = true;
        $em      = $this->getEntityManager();

        try {
            $em->beginTransaction();

            // Serialize new contract
            $em->persist($contract);

            $customer = $contract->getCustomer();
            $sysType  = $contract->getSystem()->getSystemType();

            // Get all manteinance schema belonging to system type
            $schemas = $this->getEntityManager()->createQueryBuilder()
                            ->select('ms')
                            ->from('BoilrBundle:ManteinanceSchema', 'ms')
                            ->where('ms.systemType = :type')
                            ->orderBy('ms.order')
                            ->setParameter('type', $sysType)
                            ->getQuery()
                            ->getResult();

            $lastDate = $contract->getStartDate();

            // Create as much manteinance date appointment as schema
            foreach ($schemas as $schema) {
                /* @var $schema MyManteinanceSchema */

                if ($schema->getIsPeriodic()) {
                    $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());

                    while ($lastDate <= $contract->getEndDate()) {
                        $manInt   = new ManteinanceIntervention();
                        $manInt->setOriginalDate($lastDate);
                        $manInt->setCustomer($customer);
                        $manInt->setSystem($contract->getSystem());
                        $manInt->setIsPlanned(true);
                        $manInt->setStatus(ManteinanceIntervention::STATUS_OPEN);
                        $manInt->setDefaultOperationGroup($schema->getOperationGroup());

                        $em->persist($manInt);
                        $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());
                    }
                } else {
                    $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());
                    $manInt   = new ManteinanceIntervention();
                    $manInt->setOriginalDate($lastDate);
                    $manInt->setCustomer($customer);
                    $manInt->setIsPlanned(true);
                    $manInt->setSystem($contract->getSystem());
                    $manInt->setStatus(ManteinanceIntervention::STATUS_OPEN);
                    $manInt->setDefaultOperationGroup($schema->getOperationGroup());

                    $em->persist($manInt);
                }
            }

            $em->flush();
            $em->commit();
        } catch (Exception $exc) {
            $em->rollback();
            $success = false;
        }

        return $success;
    }

    /**
     *
     * @param \DateTime $date
     * @param string $interval
     * @return \DateTime
     */
    protected function getFutureDate(\DateTime $date, $interval)
    {
        $dateInterv = \DateInterval::createFromDateString($interval);
        $newDate = new \DateTime();
        $newDate->setTimestamp($date->getTimestamp());

        return $newDate->add($dateInterv);
    }
}






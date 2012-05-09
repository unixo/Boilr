<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Contract as MyContract,
    Boilr\BoilrBundle\Entity\SystemType as MySystemType,
    Boilr\BoilrBundle\Entity\MaintenanceSchema as MyMaintenanceSchema,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Doctrine\ORM\EntityRepository;

use Boilr\BoilrBundle\Extension\MyDateTime;

/**
 * ContractRepository
 */
class ContractRepository extends EntityRepository
{
    /**
     *
     * @param MyContract $contract
     * @return boolean
     */
    public function isContractLegal(MyContract $contract)
    {
        $success = true;

        $contracts = $this->getEntityManager()->createQueryBuilder()
                          ->select('c')
                          ->from('BoilrBundle:Contract', 'c')
                          ->where('c.customer = :owner AND c.system = :sys')
                          ->andWhere('( (:date1 >= c.startDate AND :date1 <= c.endDate) OR '.
                                     '(:date2 >= c.startDate AND :date2 <= c.endDate) )')
                          ->setParameters(array('date1' => $contract->getStartDate(),
                                                'date2' => $contract->getEndDate(),
                                                'owner' => $contract->getCustomer(),
                                                'sys'   => $contract->getSystem()))
                          ->getQuery()->getResult();

        $success = (count($contracts) == 0);

        return $success;
    }

    /**
     * Persist contract to store and create all next manteinance interventions
     *
     * @param MyContract $contract
     * @return boolean
     */
    public function createNewContract(MyContract $contract)
    {
        $success = true;
        $em      = $this->getEntityManager();

        try {
            $em->beginTransaction();

            // Serialize new contract
            $em->persist($contract);

            // Get all manteinance schema belonging to system type
            $sysType  = $contract->getSystem()->getSystemType();
            $schemas = $this->getEntityManager()->createQueryBuilder()
                            ->select('ms')
                            ->from('BoilrBundle:MaintenanceSchema', 'ms')
                            ->where('ms.systemType = :type')
                            ->orderBy('ms.listOrder')
                            ->setParameter('type', $sysType)
                            ->getQuery()
                            ->getResult();

            $lastDate = MyDateTime::nextWorkingDay( $contract->getStartDate() );
            $lastDate->setTime(8,0);

            $miRepos = $this->getEntityManager()->getRepository('BoilrBundle:MaintenanceIntervention');

            // Create as much manteinance date appointment as schema
            foreach ($schemas as $schema) {
                /* @var $schema MyMaintenanceSchema */

                if ($schema->getIsPeriodic()) {
                    $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());

                    while ($lastDate <= $contract->getEndDate()) {
                        $manInt = MaintenanceIntervention::PlannedInterventionFactory($contract);
                        $manInt->setScheduledDate($lastDate);
                        $manInt->addSystem($contract->getSystem(), $schema->getOperationGroup());
                        $miRepos->evalExpectedCloseDate($manInt);

                        $em->persist($manInt);
                        $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());
                    }
                } else {
                    $lastDate = $this->getFutureDate($lastDate, $schema->getFreq());

                    $manInt = MaintenanceIntervention::PlannedInterventionFactory($contract);
                    $manInt->setScheduledDate($lastDate);
                    $manInt->addSystem($contract->getSystem(), $schema->getOperationGroup());
                    $miRepos->evalExpectedCloseDate($manInt);

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
     * Evaluate a future date based on given interval
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






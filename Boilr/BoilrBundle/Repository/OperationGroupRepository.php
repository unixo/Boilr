<?php

namespace Boilr\BoilrBundle\Repository;

use Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Entity\TemplateSection,
    Doctrine\ORM\EntityRepository;

/**
 * OperationGroupRepository
 */
class OperationGroupRepository extends EntityRepository
{
    /**
     * Returns expected time length, expressed in seconds, of given check type
     *
     * @param OperationGroup $opGroup
     * @return int
     */
    public function getEstimatedTimeLength(OperationGroup $opGroup)
    {
        $timeLength = 0;
        foreach ($opGroup->getOperations() as $op) {
            /* @var $op \Boilr\BoilrBundle\Entity\Operation */
            $timeLength += $op->getTimeLength();
        }

        return $timeLength;
    }
}
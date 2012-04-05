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
        $sections   = $this->getEntityManager()->getRepository('BoilrBundle:TemplateSection')
                           ->findBy(array('group' => $opGroup->getId()));

        foreach ($sections as $section) {
            /* @var $section \Boilr\BoilrBundle\Entity\TemplateSection */
            $timeLength += $section->getTimeLength();
        }

        return $timeLength;
    }
}
<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Operation,
    Boilr\BoilrBundle\Entity\OperationGroup;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Description of OperationController
 *
 * @author unixo
 */
class OperationController extends BaseController
{
    function __construct()
    {
        $this->entityName = 'BoilrBundle:Operation';
    }

    /**
     * @Route("/move/{id}/{dir}", name="operation_move")
     * @ParamConverter("oper", class="BoilrBundle:Operation")
     * @Template()
     */
    public function moveAction(Operation $oper, $dir = "down")
    {
        $_dir = strtolower($dir);
        if (! in_array($_dir, array('up', 'down'))) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $parentGroup = $oper->getParentGroup();
        $count       = $parentGroup->getOperations()->count()-1;
        $index       = $parentGroup->getOperations()->indexOf($oper);

        if (($index == 0 && $dir == "up") || ($index == $count && $dir == "down")) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        if ($dir == "up") {
            $prevOper = $parentGroup->getOperations()->get($index-1);
            /* @var $prevOper \Boilr\BoilrBundle\Entity\Operation */
            $prevOper->setListOrder($index);
            $oper->setListOrder($index-1);
        } else {
            $nextOper = $parentGroup->getOperations()->get($index+1);
            /* @var $nextOper \Boilr\BoilrBundle\Entity\Operation */
            $nextOper->setListOrder($index);
            $oper->setListOrder($index+1);
        }
        $this->getEntityManager()->flush();

        return $this->redirect( $this->generateUrl('operation_group_operations', array('id' => $parentGroup->getId())) );
    }
}

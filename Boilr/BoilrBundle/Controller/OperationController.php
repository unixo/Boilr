<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Operation,
    Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Form\OperationForm;

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
     * @Route("/{id}/delete", name="operation_delete")
     * @ParamConverter("oper", class="BoilrBundle:Operation")
     * @Template()
     */
    public function deleteAction(Operation $oper)
    {
        $group = $oper->getParentGroup();

        try {
            $dem = $this->getDoctrine()->getEntityManager();
            $dem->remove($oper);
            $dem->flush();
            $this->setNoticeMessage("Operazione conclusa con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('operation_group_operations',
                            array('id' => $group->getId())));
    }

    /**
     * @Route("/{gid}/add", name="operation_add")
     * @Route("/{oid}/update", name="operation_edit")
     * @Template()
     */
    public function addOrUpdateAction($gid = null, $oid = null)
    {
        $group  = null;
        $oper   = null;
        $opType = null;

        // Guess if I'm adding a new operation or updating an existing one
        if ($gid != null) {
            $group = $this->getDoctrine()->getRepository('BoilrBundle:OperationGroup')->findOneById($gid);
            if ($group) {
                $opType = "add";
                $oper   = new Operation();
                $oper->setParentGroup($group);
                $oper->setListOrder($group->getOperations()->count());
            }
        } else {
            $oper = $this->getEntityRepository()->findOneById($oid);
            if ($oper) {
                $group  = $oper->getParentGroup();
                $opType = "update";
            }
        }

        if ($oper === null && $group === null) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $form = $this->createForm(new OperationForm(), $oper);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($opType == 'add') {
                        $dem->persist($oper);
                    }
                    $dem->flush();
                    $this->setNoticeMessage("Operazione conclusa con successo");

                    return $this->redirect($this->generateUrl('operation_group_operations',
                            array('id' => $group->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'parentGroup' => $group, 'opType' => $opType);
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

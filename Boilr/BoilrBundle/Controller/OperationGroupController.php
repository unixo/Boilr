<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Form\OperationGroupForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

class OperationGroupController extends BaseController
{

    public function __construct()
    {
        $this->entityName = 'BoilrBundle:OperationGroup';
    }

    /**
     * @Route("/list", name="operation_group_list")
     * @Template()
     */
    public function listAction()
    {
        $groups = $this->getEntityRepository()->findBy(array(), array('name' => 'ASC'));

        return array('groups' => $groups);
    }

    /**
     * @Route("/{id}/list-operations", name="operation_group_operations")
     * @Template()
     */
    public function showOperationsAction()
    {
        $group = $this->paramConverter('id');
        $operations = $group->getOperations();

        return array('operations' => $operations, 'count' => count($operations), 'group' => $group);
    }

    /**
     * @Route("/{id}/delete", name="operation_group_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function deleteAction(OperationGroup $opgroup)
    {
        $opgroup = $this->paramConverter('id');
        try {
            $dem = $this->getDoctrine()->getEntityManager();
            $dem->remove($opgroup);
            $dem->flush();
            $this->setNoticeMessage("Operazione conclusa con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('operation_group_list'));
    }

    /**
     * @Route("/add", name="operation_group_add")
     * @Route("/{oid}/update", name="operation_group_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function addOrUpdateAction($oid = null)
    {
        $opGroup = null;
        $opType = null;

        // Guess if I'm adding a new group or updating an existing one
        if ($oid === null) {
            $opGroup = new OperationGroup();
            $opType = "add";
        } else {
            $opGroup = $this->getEntityRepository()->findOneById($oid);
            if (!$opGroup) {
                throw new \InvalidArgumentException("Invalid argument");
            }
            $opType = "update";
        }

        $form = $this->createForm(new OperationGroupForm(), $opGroup);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getDoctrine()->getEntityManager();
                    if ($opType == "add") {
                        $dem->persist($opGroup);
                    }
                    $dem->flush();
                    $this->setNoticeMessage("Operazione conclusa con successo");

                    return $this->redirect($this->generateUrl("operation_group_operations", array('id' => $opGroup->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), "opType" => $opType);
    }

}

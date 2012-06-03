<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Operation,
    Boilr\BoilrBundle\Entity\OperationGroup,
    Boilr\BoilrBundle\Form\OperationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Description of OperationController
 *
 * @author unixo
 */
class OperationController extends BaseController
{

    public function __construct()
    {
        $this->entityName = 'BoilrBundle:Operation';
    }

    /**
     * @Route("/list-operations", name="operation_list")
     * @Template()
     */
    public function listAction()
    {
        $operations = $this->getEntityRepository()->findAll();

        return array('operations' => $operations);
    }

    /**
     * @Route("/{id}/unlink-from-group/{gid}", name="operation_unlink")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function unlinkFromGroupAction()
    {
        $oper = $this->paramConverter("id");
        /* @var $oper \Boilr\BoilrBundle\Entity\Operation */
        $group = $this->paramConverter('gid', 'BoilrBundle:OperationGroup');
        /* @var $group \Boilr\BoilrBundle\Entity\OperationGroup */

        try {
            $group->getOperations()->removeElement($oper);
            $this->getDoctrine()->getEntityManager()->flush();
            $this->setNoticeMessage("Operazione conclusa con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('operation_group_operations', array('id' => $group->getId())));
    }

    /**
     * @Route("/{id}/delete", name="operation_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function deleteAction()
    {
        $oper = $this->paramConverter("id");

        try {
            $dem = $this->getDoctrine()->getEntityManager();
            $dem->remove($oper);
            $dem->flush();
            $this->setNoticeMessage("Operazione conclusa con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->getLastRoute();
    }

    /**
     * @Route("/add", name="operation_add")
     * @Route("/{id}/update", name="operation_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function addOrUpdateAction()
    {
        $oper = null;

        // Guess if I'm adding a new operation or updating an existing one
        if (! $this->getRequest()->get('id')) {
             $oper = new Operation();
        } else {
            $oper = $this->paramConverter('id');
        }

        $form = $this->createForm(new OperationForm(), $oper);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if (! $oper->getId()) {
                        foreach ($oper->getParentGroups() as $opGroup) {
                            $opGroup->addOperation($oper);
                        }
                        $dem->persist($oper);
                    }
                    $dem->flush();
                    $this->setNoticeMessage("Operazione conclusa con successo");

                    return $this->getLastRoute();
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'oper' => $oper);
    }

}

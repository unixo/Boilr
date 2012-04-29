<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Boilr\BoilrBundle\Entity\Attachment as MyAttachment,
    Boilr\BoilrBundle\Entity\System,
    Boilr\BoilrBundle\Entity\Group,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Form\AttachmentForm;

/**
 * \Boilr\BoilrBundle\Controller\AttachmentController
 *
 * @author unixo
 */
class AttachmentController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Attachment';
    }

    /**
     * @Route("/{id}/delete", name="attachment_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Template()
     */
    public function deleteAction()
    {
        $attachment = $this->paramConverter('id');
        // Check file ownership if current user is an installer
        $currentUser = $this->getCurrentUser();
        if ($currentUser->hasRole(Group::ROLE_INSTALLER)) {
            if ($this->getCurrentUser()->getLogin() != $attachment->getOwner()->getLogin()) {
                throw new AccessDeniedException('permission denied: not owner');
            }
        }

        try {
            $dem = $this->getEntityManager();
            $dem->remove($attachment);
            $dem->flush();
            $this->setNoticeMessage("Allegato eliminato con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
        }

        return $this->getLastRoute();
    }

    /**
     * @Route("/{id}/attach-to-system", name="system_upload_doc")
     * @Template()
     */
    public function uploadSystemDocAction()
    {
        $system = $this->paramConverter('id', "BoilrBundle:System");
        // create and init attachment
        $attachment = new MyAttachment();
        $attachment->setUploadDate(new \DateTime());
        $attachment->setType(MyAttachment::TYPE_SYSTEM);
        $attachment->setOwner($this->getCurrentUser());
        $attachment->setParentSystem($system);

        // build the form and pass data to it
        $form = $this->createForm(new AttachmentForm(), $attachment);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getDoctrine()->getEntityManager();
                    $dem->persist($attachment);
                    $dem->flush();
                    $this->setNoticeMessage('Documento allegato con successo');

                    return $this->redirect($this->generateUrl('system_list_doc', array('id' => $system->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'system' => $system);
    }

    /**
     * @Route("/{id}/attach-to-intervention", name="intervention_upload_doc")
     * @Template()
     */
    public function uploadInterventionDocAction()
    {
        $intervention = $this->paramConverter("id", "BoilrBundle:ManteinanceIntervention");
        // create and init attachment
        $attachment = new MyAttachment();
        $attachment->setUploadDate(new \DateTime());
        $attachment->setType(MyAttachment::TYPE_INTERVENTION);
        $attachment->setOwner($this->getCurrentUser());
        $attachment->setParentIntervention($intervention);

        // build the form and pass data to it
        $form = $this->createForm(new AttachmentForm(), $attachment);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getDoctrine()->getEntityManager();
                    $dem->persist($attachment);
                    $dem->flush();
                    $this->setNoticeMessage('Documento allegato con successo');

                    return $this->redirect($this->generateUrl('intervention_list_doc',
                            array('id' => $intervention->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'interv' => $intervention);
    }

}

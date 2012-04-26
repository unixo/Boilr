<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Attachment as MyAttachment,
    Boilr\BoilrBundle\Form\SystemForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class SystemController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:System';
    }

    /**
     * @Route("/{id}/show-details", name="system_show")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Template(vars={"system"})
     */
    public function showAction(MySystem $system)
    {
    }

    /**
     * @Route("/delete/{id}", name="system_delete")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function deleteAction(MySystem $system)
    {
        $person = $system->getOwner();
        $success = $this->getEntityRepository()->deleteSystem($system);

        if ($success) {
            $this->setNoticeMessage("Impianto eliminato con successo");
        } else {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
        }

        return $this->redirect($this->generateUrl('show_person', array('id' => $person->getId())));
    }

    /**
     * @Route("/add/{pid}", name="system_add")
     * @Route("/update/{sid}", name="system_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function addOrUpdateAction($pid = null, $sid = null)
    {
        $system = null;
        /* @var $system MySystem */
        $person = null;
        /* @var $person MyPerson */

        if (isset($sid)) {
            $system = $this->getEntityRepository()->findOneById($sid);
            if (!$system) {
                throw new \InvalidArgumentException("Invalid argument");
            } else {
                $person = $system->getOwner();
            }
        } else {
            $person = $this->getDoctrine()->getRepository('BoilrBundle:Person')
                    ->findOneById($pid);
            if (!$person) {
                throw new \InvalidArgumentException("Invalid argument");
            }
            $system = new MySystem();
            $system->setOwner($person);
            $person->getSystems()->add($system);
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new SystemForm(), $system, array(
            'validation_groups' => array('system'),
            'em' => $this->getEntityManager(),
                ));

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $em = $this->getEntityManager();
                    $em->flush();
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect($this->generateUrl('show_person', array('id' => $person->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person);
    }

    /**
     * @Route("/{id}/attachments", name="system_list_doc")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Template()
     */
    public function listAttachmentsAction(MySystem $system)
    {
        $attachments = $this->getDoctrine()->getRepository('BoilrBundle:Attachment')
                            ->findBy(array('type' => MyAttachment::TYPE_SYSTEM,
                                           'parentSystem' => $system->getId())
                                    );

        return array('attachments' => $attachments, 'system' => $system);
    }
}

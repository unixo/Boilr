<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Form\SystemForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SystemController extends BaseController
{
    /**
     * @Route("/delete/{id}", name="system_delete")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Template()
     */
    public function deleteAction(MySystem $system)
    {
        if (! $system) {
            throw new NotFoundHttpException("Invalid system");
        }

        $person  = $system->getOwner();
        $success = $this->getDoctrine()->getRepository('BoilrBundle:System')->deleteSystem($system);

        if ($success) {
            $this->setFlashMessage(self::FLASH_NOTICE, "Impianto eliminato con successo");
        } else {
            $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un errore durante l'eliminazione.");
        }

        return $this->redirect( $this->generateUrl('show_person', array('id' => $person->getId())) );
    }

    /**
     * @Route("/add/{pid}", name="system_add")
     * @Route("/update/{sid}", name="system_edit")
     * @Template()
     */
    public function addOrUpdateAction($pid = null, $sid = null)
    {
        $system = null;
        /* @var $system MySystem */
        $person = null;
        /* @var $person MyPerson */

        if (isset($sid)) {
            $system = $this->getDoctrine()->getRepository('BoilrBundle:System')
                            ->findOneById($sid);
            if (!$system) {
                throw new NotFoundHttpException("Invalid system");
            } else {
                $person = $system->getOwner();
            }
        } else {
            $person = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
            if (! $person) {
                throw new NotFoundHttpException("Invalid person");
            }
            $system = new MySystem();
            $system->setOwner($person);
            $person->getSystems()->add($system);
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new SystemForm(), $system,
                               array( 'validation_groups' => array('system') ));

        if ($this->isPOSTRequest()) {
            $form->bindRequest( $this->getRequest() );

            if ($form->isValid()) {
                try {
                    $em = $this->getEntityManager();
                    $em->flush();
                    $this->setFlashMessage(self::FLASH_NOTICE, 'Operazione completata con successo');

                    return $this->redirect( $this->generateUrl('show_person', array('id' => $person->getId() )));
                } catch (Exception $exc) {
                    $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person);
    }
}

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
    function __construct()
    {
        $this->entityName = 'BoilrBundle:System';
    }

    /**
     * @Route("/delete/{id}", name="system_delete")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Template()
     */
    public function deleteAction(MySystem $system)
    {
        $person  = $system->getOwner();
        $success = $this->getEntityRepository()->deleteSystem($system);

        if ($success) {
            $this->setNoticeMessage("Impianto eliminato con successo");
        } else {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
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
            $system = $this->getEntityRepository()->findOneById($sid);
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
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect( $this->generateUrl('show_person', array('id' => $person->getId() )));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person);
    }
}

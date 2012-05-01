<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\User as MyUser,
    Boilr\BoilrBundle\Form\UserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Description of UserController
 *
 * @author unixo
 */
class UserController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:User';
    }

    /**
     * @Route("/list", name="user_list")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function listAction()
    {
        $users = $this->getEntityRepository()->findBy(array(), array('surname' => 'ASC', 'name' => 'ASC'));

        return array('users' => $users);
    }

    /**
     * @Route("/add", name="user_add")
     * @Route("/{pid}/update", name="user_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function addOrUpdateAction($pid = null)
    {
        $user = null;

        if ($pid === null) {
            $user = new MyUser();
            $user->setIsActive(true);
        } else {
            $user = $this->paramConverter("pid");
            if (!$user) {
                throw new \InvalidArgumentException("Invalid argument");
            }
        }

        $form = $this->createForm(new UserForm(), $user);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($pid === null) {
                        $dem->persist($user);
                    }
                    $dem->flush();
                    $this->setNoticeMessage('Operazione conclusa con successo');

                    return $this->redirect($this->generateUrl('user_list'));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'optype' => ($pid ? 'update' : 'add'), 'user' => $user);
    }

    /**
     * @Route("/{id}/delete", name="user_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function deleteAction()
    {
        try {
            $user = $this->paramConverter("id");
            $dem = $this->getEntityManager();
            $dem->remove($user);
            $dem->flush();
            $this->setNoticeMessage("Utente eliminato con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('user_list'));
    }

}
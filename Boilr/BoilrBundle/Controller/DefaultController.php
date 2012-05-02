<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;
use Boilr\BoilrBundle\Entity\User as MyUser,
    Boilr\BoilrBundle\Entity\Group as MyGroup;

class DefaultController extends BaseController
{

    /**
     * @Route("/", name="homepage")
     * @Method("get")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->getCurrentUser();
        if ($user->hasRole(MyGroup::ROLE_INSTALLER)) {
            return $this->redirect($this->generateUrl('installer_homepage'));
        }

        return array();
    }

    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error);
    }

    /**
     * @Route("/admin", name="admin_homepage")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Method("get")
     * @Template("BoilrBundle:Default:template5-admin.html.twig")
     */
    public function adminHomeAction()
    {
        return array();
    }

    /**
     * @Route("/installer", name="installer_homepage")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Method("get")
     * @Template("BoilrBundle:Default:template5-installer.html.twig")
     */
    public function installerHomeAction()
    {
        return array();
    }

}

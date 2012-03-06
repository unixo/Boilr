<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
 */
class ManteinanceSchemaController extends BaseController
{
    /**
     * @Route("/", name="ms_list")
     * @Template()
     */
    public function indexAction()
    {
        $schemas = $this->getEntityManager()->createQuery(
                "SELECT s FROM BoilrBundle:ManteinanceSchema s"
                )->getResult();

        return array('schemas' => $schemas);
    }
}

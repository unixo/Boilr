<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\OperationGroup;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class OperationGroupController extends BaseController
{
    function __construct()
    {
        $this->entityName = 'BoilrBundle:OperationGroup';
    }

    /**
     * @Route("/list", name="list_operation_group")
     * @Template()
     */
    public function listAction()
    {
        $records = $this->getEntityRepository()->findBy(array(), array('name' => 'ASC'));

        return array('records' => $records);
    }

    /**
     * @Route("/{id}/list-operations", name="operation_group_operations")
     * @ParamConverter("group", class="BoilrBundle:OperationGroup")
     * @Template()
     */
    public function showOperationsAction(OperationGroup $group)
    {
        $operations = $this->getDoctrine()->getRepository('BoilrBundle:Operation')
                           ->findBy(array('parentGroup' => $group->getId()),
                                    array('listOrder' => 'ASC'));

        return array('operations' => $operations, 'count' => count($operations));
    }
}
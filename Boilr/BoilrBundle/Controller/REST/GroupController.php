<?php

namespace Boilr\BoilrBundle\Controller\REST;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Route,
    FOS\RestBundle\Controller\Annotations\NoRoute,
    FOS\RestBundle\Controller\Annotations\Get,
    FOS\RestBundle\Controller\Annotations\Prefix,
    FOS\RestBundle\Controller\Annotations\NamePrefix,
    FOS\RestBundle\View\View,
    FOS\RestBundle\Controller\Annotations\Post;
use Boilr\BoilrBundle\Controller\BaseController;

/**
 * @Prefix("/rest")
 * @NamePrefix("rest_")
 */
class GroupController extends BaseController
{
    public function __construct()
    {
        $this->entityName = 'BoilrBundle:Group';
    }

    public function getGroupAction($gid)
    {
        $gid = $this->paramConverter('gid');

        $data = array(
            'id' => $user->getId(),
            'name' => $user->getName(),
            );

        $view = View::create()
                ->setStatusCode(200)
                ->setData($data);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

}


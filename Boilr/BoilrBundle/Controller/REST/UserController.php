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
class UserController extends BaseController
{

    public function __construct()
    {
        $this->entityName = 'BoilrBundle:User';
    }

    public function getUsersAction()
    {
        $users = $this->getEntityRepository()->findAll();
        $data = array();

        foreach ($users as $user) {
         $data[] = array(
            "id" => $user->getId(),
            "name" => $user->getName(),
            "surname" => $user->getSurname(),
            "login" => $user->getLogin(),
            );
        }

        $view = View::create()
                ->setStatusCode(200)
                ->setData($data);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function getUserAction($id)
    {
        $user = $this->paramConverter("id");

        $data = array(
            "id" => $user->getId(),
            "name" => $user->getName(),
            "surname" => $user->getSurname(),
            "login" => $user->getLogin(),
            );

        $view = View::create()
                ->setStatusCode(200)
                ->setData($data);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

}


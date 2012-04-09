<?php

namespace Boilr\BoilrBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\HttpKernel\HttpKernelInterface;

class RefererListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $router;

    public function __construct(\Symfony\Component\Routing\Router $router)
    {
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        /** @var \Symfony\Component\HttpFoundation\Request $request  */
        $request = $event->getRequest();
        /** @var \Symfony\Component\HttpFoundation\Session $session  */
        $session = $request->getSession();

        $routeParams = $this->router->match($request->getPathInfo());
        $routeName = $routeParams['_route'];
        if ($routeName[0] == '_') {
            return;
        }
        unset($routeParams['_route']);
        $routeData = array('name' => $routeName, 'params' => $routeParams);

        //Skipping duplicates
        $thisRoute = $session->get('this_route', array());
        if ($thisRoute == $routeData) {
            return;
        }
        $session->set('last_route', $thisRoute);
        $session->set('this_route', $routeData);
    }
}

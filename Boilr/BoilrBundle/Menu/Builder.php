<?php

namespace Boilr\BoilrBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function topMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        $menu->addChild('Home', array('route' => 'homepage'));

        $item = $menu->addChild('Anagrafica', array('route' => 'main_person'));
        $item->addChild('Nuovo',   array('route' => 'new_person'));
        $item->addChild('Ricerca', array('route' => 'search_person'));

        $item = $menu->addChild('Interventi', array('route' => 'main_intervention'));


        // Add link to administration if current user has admin role
        $securityContext = $this->container->get('security.context');
        $token = $securityContext->getToken();
        $roles = array('ROLE_ADMIN', 'ROLE_SUPERUSER');
        if ($token && $securityContext->isGranted($roles)) {
            $item = $menu->addChild('Amministrazione', array('route' => 'admin_homepage'));
            $item->addChild('Schemi Manutenz.', array('route' => 'manteinance_schema_list'));
        }

        $menu->addChild('logout', array('route' => '_security_logout'))->setLabel('Logout');

        return $menu;
    }
}
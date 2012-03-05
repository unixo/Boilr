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

      //  $menu->addChild('Tipologie contratto', array('route' => 'list_contract_type'));

        $menu->addChild('logout', array('route' => '_security_logout'))->setLabel('Logout');

        return $menu;
    }
}
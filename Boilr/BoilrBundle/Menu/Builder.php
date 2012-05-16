<?php

namespace Boilr\BoilrBundle\Menu;

use Knp\Menu\FactoryInterface,
    Symfony\Component\DependencyInjection\ContainerAware;
use Boilr\BoilrBundle\Entity\Group as MyGroup;

class Builder extends ContainerAware
{

    public function personSubMenu(FactoryInterface $factory)
    {
        $securityContext = $this->container->get('security.context');

        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        if ($securityContext->isGranted(MyGroup::ROLE_OPERATOR)) {
            $menu->addChild('Nuovo cliente', array('route' => 'new_person'))->setExtra('icon', 'icon-user');
            $menu->addChild('Contratti stipulati', array('route' => 'contract_list'))->setExtra('icon', 'icon-list-alt');
        }

        $menu->addChild('Ricerca', array('route' => 'search_person'))->setExtra('icon', 'icon-search');

        return $menu;
    }

    public function installerMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        $menu->addChild('I miei impianti', array('route' => 'installer_list_systems'))->setExtra('icon', 'icon-fire');
        $menu->addChild('I miei interventi', array('route' => 'installer_list_interventions'))->setExtra('icon', 'icon-wrench');
        $menu->addChild('I miei allegati', array('route' => 'installer_list_docs'))->setExtra('icon', 'icon-book');
        $menu->addChild('Ditte di manutenzione', array('route' => 'company_list'))->setExtra('icon', 'icon-cog');
        $menu->addChild('Nuovo installatore', array('route' => 'installer_add'))->setExtra('icon', 'icon-user');

        return $menu;
    }

    public function interventionMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        $menu->addChild('Mese corrente', array('route' => 'current_month_interventions'))->setExtra('icon', 'icon-th-list');
        $menu->addChild('Ricerca', array('route' => 'search_intervention'))->setExtra('icon', 'icon-search');
        $menu->addChild('Interventi non gestiti', array('route' => 'policy_assignment_wizard'))->setExtra('icon', 'icon-tasks');

        return $menu;
    }

    public function adminMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        $menu->addChild('Schemi di manutenzione', array('route' => 'manteinance_schema_list'))->setExtra('icon', 'icon-cog');
        $menu->addChild('Elenco operazioni atomiche', array('route' => 'operation_list'))->setExtra('icon', 'icon-tint');
        $menu->addChild('Gruppi di controlli', array('route' => 'operation_group_list'))->setExtra('icon', 'icon-eye-open');
        $menu->addChild('Allegati', array('route' => 'template_list'))->setExtra('icon', 'icon-file');
        $menu->addChild('Ditte di manutenzione', array('route' => 'company_list'))->setExtra('icon', 'icon-cog');
        $menu->addChild('Gestione utenti', array('route' => 'user_list'))->setExtra('icon', 'icon-user');

        return $menu;
    }

}
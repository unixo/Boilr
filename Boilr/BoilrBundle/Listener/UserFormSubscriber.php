<?php

namespace Boilr\BoilrBundle\Listener;

use Symfony\Component\Form\Event\DataEvent,
    Symfony\Component\Form\FormFactoryInterface,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\Form\FormEvents;

/**
 * Description of UserFormSubscriber
 *
 * @author unixo
 */
class UserFormSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        
        if (null === $user) {
            return;
        }

        // check if the user object is "new"
        if (! $user->getId()) {
            $form->add($this->factory->createNamed('text', 'login'));
        }
    }
}
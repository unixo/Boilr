<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
    const FLASH_ERROR  = 'error';
    const FLASH_NOTICE = 'notice';

    protected $entityName;

    /**
     * Returns the current session
     *
     * @return Symfony\Component\HttpFoundation\Session
     */
    protected function getSession()
    {
        return $this->get('session');
    }

    /**
     * Return true if current request was POSTed.
     *
     * @return boolean
     */
    public function isPOSTRequest()
    {
        return ($this->getRequest()->getMethod() == 'POST');
    }

    /**
     * Shortcut to access Doctrine Entity Manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * Shortcut to display flash messages to the user
     *
     * @param string $messageType
     * @param string $message
     */
    public function setFlashMessage($messageType, $message)
    {
        $this->getSession()->setFlash($messageType, $message);
    }

    /**
     * Display a flash notice message to the user
     *
     * @param string $message
     * @param array $placeholders
     */
    public function setNoticeMessage($message, $placeholders = null)
    {
        if (is_array($placeholders)) {
            $message = vsprintf($message, $placeholders);
        }

        $this->setFlashMessage(self::FLASH_NOTICE, $message);
    }

    /**
     * Display a flash error message to the user
     *
     * @param string $message
     * @param array $placeholders
     */
    public function setErrorMessage($message, $placeholders = null)
    {
        if (is_array($placeholders)) {
            $message = vsprintf($message, $placeholders);
        }

        $this->setFlashMessage(self::FLASH_ERROR, $message);
    }

    public function _log($message, $params = array())
    {
        $logger = $this->get('logger');
        $logger->info($message, $params);
    }

    /**
     * Returns an instance of doctrine repository for entity managed by the controller
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getEntityRepository()
    {
        return $this->getDoctrine()->getRepository($this->entityName);
    }
}
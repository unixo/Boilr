<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseController extends Controller
{

    const FLASH_ERROR = 'error';
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
     * @param array  $placeholders
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
     * @param array  $placeholders
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

    /**
     * Returns current logged user
     *
     * @return \Boilr\BoilrBundle\Entity\User
     */
    public function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    /**
     * Returns a redirect response filled with referer
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getLastRoute()
    {
        $lastRoute = $this->getSession()->get('last_route');
        $url = $this->generateUrl($lastRoute['name'], $lastRoute['params']);
        $response = $this->redirect($url);

        return $response;
    }

    /**
     * Get installer linked to current logged user, if any
     *
     * @return \Boilr\BoilrBundle\Entity\Installer
     */
    public function getCurrentInstaller()
    {
        $user = $this->getCurrentUser();
        $installer = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->findOneByAccount($user->getId());

        if (!$installer) {
            throw new \ErrorException('current user is not an installer');
        }

        return $installer;
    }

    public function paramConverter($paramName, $className = null)
    {
        $paramValue = $this->getRequest()->get($paramName);
        if ($paramValue === null) {
            throw new \InvalidArgumentException("request does not contain $paramName parameter");
        }

        $class = $className ? $className : $this->entityName;
        $obj = $this->getDoctrine()->getRepository($class)->findOneById($paramValue);
        if ($obj === null) {
            throw new NotFoundHttpException("object not found ($paramValue)");
        }

        return $obj;
    }

    protected function _debug($var)
    {
         $this->get('ladybug')->log($var);
    }
}

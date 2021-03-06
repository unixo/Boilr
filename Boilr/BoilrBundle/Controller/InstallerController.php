<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Installer as MyInstaller,
    Boilr\BoilrBundle\Form\InstallerForm,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Symfony\Component\HttpFoundation\Response,
    JMS\SecurityExtraBundle\Annotation\Secure;

class InstallerController extends BaseController
{

    public function __construct()
    {
        $this->entityName = 'BoilrBundle:Installer';
    }

    /**
     * @Route("/", name="installer_homepage")
     * @Method("get")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/add", name="installer_add")
     * @Route("/{id}/update", name="installer_update")
     * @Template
     */
    public function addOrUpdateAction($id = null)
    {
        $installer = null;
        $opType = null;
        if ($id == null) {
            $installer = new MyInstaller();
            $opType = "add";
        } else {
            $installer = $this->paramConverter('id');
            $opType = "update";
        }
        $form = $this->createForm(new InstallerForm(), $installer);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($opType == 'add') {
                        $dem->persist($installer);
                    }
                    $dem->flush();
                    $this->setNoticeMessage('Operazione conclusa con successo');

                    return $this->redirect($this->generateUrl('company_employee_list',
                            array('id' => $installer->getCompany()->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'opType' => $opType);
    }

    /**
     * @Route("/{id}/show", name="installer_show")
     * @Method("get")
     * @Template()
     */
    public function showAction()
    {
        $installer = $this->paramConverter("id");

        return compact('installer');
    }

    /**
     * @Route("/installers-for-systemtype", name="installers_for_systype")
     * @Method("get")
     */
    public function ajaxInstallerForSystemTypeAction()
    {
        $id = $this->getRequest()->get('id');
        $sysType = $this->getDoctrine()->getRepository('BoilrBundle:SystemType')->findOneById($id);
        /* @var $sysType \Boilr\BoilrBundle\Entity\SystemType */
        $installers = $sysType->getInstallers();
        $result = array();

        foreach ($installers as $tech) {
            /* @var $tech \Boilr\BoilrBundle\Entity\Installer */
            $result[] = array(
                'id' => $tech->getId(),
                'company' => $tech->getCompany()->getName(),
                'fullName' => $tech->getFullName(),
                'phone' => $tech->getOfficePhone(),
            );
        }

        $response = new Response();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/ajax-search-installer", name="ajax_pick_installer")
     * @Method("get")
     */
    public function ajaxInstallerSearchAction()
    {
        $value = $this->getRequest()->get('term');
        $installers = $this->getEntityManager()->createQuery(
                        "SELECT i FROM BoilrBundle:Installer i " .
                        "WHERE (i.name LIKE :value OR i.surname LIKE :value) " .
                        "ORDER BY i.surname, i.name")
                ->setParameter('value', "%$value%")
                ->getResult();

        $json = array();
        foreach ($installers as $person) {
            $json[] = array(
                'label' => $person->getFullName(),
                'value' => $person->getId()
            );
        }

        $response = new Response();
        $response->setContent(json_encode($json));

        return $response;
    }

    /**
     * @Route("/show-my-interventions", name="installer_list_interventions")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Method("get")
     * @Template()
     */
    public function showMyInterventionsAction()
    {
        $installer = $this->getCurrentInstaller();
        $interventions = $this->getDoctrine()->getRepository('BoilrBundle:MaintenanceIntervention')
                        ->interventionsForInstaller($installer);

        return array('interventions' => $interventions);
    }

    /**
     * @Route("/show-my-systems", name="installer_list_systems")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Method("get")
     * @Template()
     */
    public function showMySystemsAction()
    {
        $installer = $this->getCurrentInstaller();
        $systems = $this->getDoctrine()->getRepository('BoilrBundle:System')
                        ->findBy(array('defaultInstaller' => $installer->getId()));

        return array('systems' => $systems);
    }

    /**
     * @Route("/show-my-documents", name="installer_list_docs")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Method("get")
     * @Template()
     */
    public function showMyDocumentsAction()
    {
        $installer = $this->getCurrentInstaller();
        $account = $installer->getAccount();
        $attachments = $this->getDoctrine()->getRepository('BoilrBundle:Attachment')
                        ->findBy(array('owner' => $account->getId()));

        return array('attachments' => $attachments);
    }
}

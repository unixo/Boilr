<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Installer as MyInstaller,
    Boilr\BoilrBundle\Form\InstallerForm,
    Boilr\BoilrBundle\Entity\ManteinanceIntervention;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Symfony\Component\HttpFoundation\Response,
    JMS\SecurityExtraBundle\Annotation\Secure;

class InstallerController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Installer';
    }

    /**
     * @Route("/", name="installer_homepage")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/new", name="installer_add")
     * @Template
     */
    public function newAction()
    {
        $installer = new MyInstaller();
        $form = $this->createForm(new InstallerForm(), $installer);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    $dem->persist($installer);
                    $dem->flush();
                    $this->setNoticeMessage('Operazione conclusa con successo');

                    return $this->redirect($this->generateUrl('company_employee_list',
                            array('id' => $installer->getCompany()->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/{id}/show", name="installer_show")
     * @ParamConverter("installer", class="BoilrBundle:Installer")
     * @Template(vars={"installer"})
     */
    public function showAction(MyInstaller $installer)
    {
    }

    /**
     * @Route("/installers-for-systemtype", name="installers_for_systype")
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
     * @Template()
     */
    public function showMyInterventionsAction()
    {
        $installer = $this->getCurrentInstaller();
        $interventions = $this->getDoctrine()->getRepository('BoilrBundle:ManteinanceIntervention')
                        ->interventionsForInstaller($installer);

        return array('interventions' => $interventions);
    }

    /**
     * @Route("/show-my-systems", name="installer_list_systems")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
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
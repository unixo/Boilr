<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\UnplannedInterventionForm,
    Boilr\BoilrBundle\Entity\InterventionDetail,
    Boilr\BoilrBundle\Entity\Group as MyGroup,
    Boilr\BoilrBundle\Entity\Attachment as MyAttachment,
    Boilr\BoilrBundle\Form\MaintenanceInterventionSearchForm,
    Boilr\BoilrBundle\Form\DetailResultsForm,
    Boilr\BoilrBundle\Form\InterventionLinkInstallerForm,
    Boilr\BoilrBundle\Form\ChooseTemplateForm,
    Boilr\BoilrBundle\Form\Model\MaintenanceInterventionFilter,
    Boilr\BoilrBundle\Form\Model\InterventionDetailResults,
    Boilr\BoilrBundle\Form\MaintenanceInterventionForm,
    Boilr\BoilrBundle\Extension\MyDateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Symfony\Component\HttpFoundation\Response,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MaintenanceInterventionController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:MaintenanceIntervention';
    }

    /**
     * @Route("/", name="main_intervention")
     * @Method("get")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/current-month", name="current_month_interventions")
     * @Method("get")
     * @Template()
     */
    public function listCurrentMonthAction()
    {
        $now = new \DateTime();
        $year = $now->format('Y');
        $month = $now->format('m');

        return $this->redirect($this->generateUrl('list_all_interventions', array('month' => $month, 'year' => $year)));
    }

    /**
     * @Route("/list-all/{year}/{month}", name="list_all_interventions")
     * @Method("get")
     * @Template()
     */
    public function listAllAction($year, $month)
    {
        // Build date interval
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $startDate->setDate($year, $month, 1);
        $monthName = $startDate->format('F');
        $lastDay = date("d", strtotime("last day of $monthName $year"));
        $endDate->setDate($year, $month, $lastDay);

        // Evaluate next/prev month
        $interval = \DateInterval::createFromDateString('1 month');
        $nextMonth = new \DateTime();
        $nextMonth->setDate($year, $month, 1);
        $nextMonth->add($interval);
        $prevMonth = new \DateTime();
        $prevMonth->setDate($year, $month, 1);
        $prevMonth->sub($interval);

        // page title
        $title = $monthName . " " . $year;

        // Search interventions
        $records = $this->getEntityRepository()
                ->interventionsBetweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        // Format titles
        $results = array();
        foreach ($records as $intervention) {
            $date = $intervention->getScheduledDate();
            $day = $date->format('d');
            $results[$day][] = $this->getInterventionTitle($intervention);
        }

        return array('records' => $results, 'year' => $year, 'month' => $month,
            'prevMonth' => $prevMonth, 'nextMonth' => $nextMonth, 'title' => $title);
    }

    /**
     * @Route("/{id}/show", name="intervention_detail")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Method("get")
     * @Template()
     */
    public function showAction()
    {
        $interv = $this->paramConverter('id');

        return compact('interv');
    }

    /**
     * @Route("/{id}/details-for-installer", name="intervention_detail_for_installer")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_INSTALLER")
     * @Method("get")
     * @Template(vars={"interv"})
     */
    public function showForInstallerAction()
    {
        $interv = $this->paramConverter('id');

        return compact('interv');
    }

    /**
     * Returns HTML representation of this intervention
     *
     * @return string
     */
    private function getInterventionTitle(MaintenanceIntervention $int)
    {
        $help = null;
        $icon = null;
        $url = $this->generateUrl('intervention_detail', array('id' => $int->getId()));

        switch ($int->getStatus()) {
            case MaintenanceIntervention::STATUS_TENTATIVE:
                $icon = "icon-question-sign";
                $help = "Da confermare";
                break;
            case MaintenanceIntervention::STATUS_ABORTED:
                $icon = "icon-remove";
                $help = "Annullato";
                break;
            case MaintenanceIntervention::STATUS_CLOSED:
                $icon = "icon-check";
                $help = "Concluso";
                break;
            case MaintenanceIntervention::STATUS_CONFIRMED:
                $icon = "icon-thumbs-up";
                $help = "Confermato";
                break;
            default:
                $icon = "icon-alert";
                $help = "stato sconosciuto";
                break;
        }

        $title = $int->getCustomer()->getSurname();
        $time = $int->getScheduledDate()->format('H:i');
        $html = sprintf('<li><a href="%s">%s <i class="%s" title="%s"></i>%s</a></li>', $url, $time, $icon, $help, $title);

        return $html;
    }

    /**
     * @Route("/add-unplanned/{id}", name="unplanned_intervention_add")
     * @Template()
     */
    public function addUnplannedInterventionAction()
    {
        $customer = $this->paramConverter('id', "BoilrBundle:Person");
        $interv = MaintenanceIntervention::interventionForCustomer($customer);
        $form = $this->createForm(new MaintenanceInterventionForm(), $interv, array('validation_groups' => array('unplanned')));

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $miRepo = $this->getEntityRepository();
                $retValue = $miRepo->persistUnplannedIntervention($interv);

                if ($retValue['success'] === true) {
                    $this->setNoticeMessage('Operazione conclusa con successo');
                    $year = $interv->getScheduledDate()->format('Y');
                    $month = $interv->getScheduledDate()->format('m');

                    return $this->redirect($this->generateUrl('list_all_interventions', array('year' => $year, 'month' => $month)));
                } else {
                    $this->setErrorMessage($retValue['message']);
                }
            }
        }

        return array('form' => $form->createView(), 'customer' => $customer);
    }

    /**
     * @Route("/update-unplanned/{id}", name="unplanned_intervention_edit")
     * @Template()
     */
    public function updateUnplannedInterventionAction()
    {
        // @todo terminare il metodo
        $interv = $this->paramConverter('id');
        $customer = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
        $mi = new MaintenanceIntervention();
        $mi->setCustomer($customer);

        foreach ($customer->getSystems() as $system) {
            $detail = new \Boilr\BoilrBundle\Entity\InterventionDetail();
            $detail->setIntervention($mi);
            $detail->setSystem($system);

            $mi->addInterventionDetail($detail);
        }

        $form = $this->createForm(new \Boilr\BoilrBundle\Form\MaintenanceInterventionForm, $mi);

        return array('form' => $form->createView(), 'customer' => $customer);
    }

    /**
     * @Route("/{id}/add-installer", name="add_installer_to_interv")
     * @Template()
     */
    public function addInstallerAction()
    {
        $interv = $this->paramConverter('id');
        $form = $this->createForm(new InterventionLinkInstallerForm(), $interv);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $dem = $this->getEntityManager();
                try {
                    $dem->persist($interv);
                    $dem->flush();
                    $this->setNoticeMessage('Tecnico associato con successo');

                    return $this->redirect($this->generateUrl('intervention_detail', array('id' => $interv->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio.');
                }
            }
        }

        return array('form' => $form->createView(), 'interv' => $interv);
    }

    /**
     * @Route("/search", name="search_intervention")
     * @Template
     */
    public function searchAction()
    {
        $filter = new MaintenanceInterventionFilter();
        $form = $this->createForm(new MaintenanceInterventionSearchForm, $filter);
        $results = array();

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $results = $this->getEntityRepository()->searchInterventions($filter);
            }
        }

        return array('form' => $form->createView(), 'results' => $results);
    }

    /**
     * Cancel given intervention
     *
     * @Route("/{id}/abort", name="intervention_abort")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Method("get")
     * @Template()
     */
    public function abortAction()
    {
        $interv = $this->paramConverter("id");
        $notAllowed = array(MaintenanceIntervention::STATUS_ABORTED,
            MaintenanceIntervention::STATUS_CLOSED);
        if (in_array($interv->getStatus(), $notAllowed)) {
            throw new \InvalidArgumentException('invalid intervention status');
        }

        try {
            $interv->setStatus(MaintenanceIntervention::STATUS_ABORTED);
            $this->getEntityManager()->flush();
            $this->setNoticeMessage("L'intervento è stato annullato");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un problema durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('intervention_detail', array('id' => $interv->getId())));
    }

    /**
     * Confirm given intervention
     *
     * @Route("/{id}/confirm", name="intervention_confirm")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Method("get")
     * @Template()
     */
    public function confirmAction()
    {
        $interv = $this->paramConverter('id');

        if ($interv->getStatus() != MaintenanceIntervention::STATUS_TENTATIVE) {
            throw new \InvalidArgumentException('invalid intervention status');
        }

        try {
            $interv->setStatus(MaintenanceIntervention::STATUS_CONFIRMED);
            $this->getEntityManager()->flush();
            $this->setNoticeMessage("L'intervento è stato confermato");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un problema durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('intervention_detail', array('id' => $interv->getId())));
    }

    /**
     * @Route("/{id}/attachments", name="intervention_list_doc")
     * @Method("get")
     * @Template()
     */
    public function listAttachmentsAction()
    {
        $intervention = $this->paramConverter('id');
        $attachments = $this->getDoctrine()->getRepository('BoilrBundle:Attachment')
                ->findBy(array('type' => MyAttachment::TYPE_INTERVENTION,
            'parentIntervention' => $intervention->getId())
        );

        return array('attachments' => $attachments, 'interv' => $intervention);
    }

    /**
     * Close given intervention
     *
     * @Route("/{id}/close", name="intervention_close")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR, ROLE_INSTALLER")
     * @Template()
     */
    public function closeAction()
    {
        $interv = $this->paramConverter('id');

        // if current user is an installer, check if the intervention has been linked to him
        if ($this->getCurrentUser()->hasRole(MyGroup::ROLE_INSTALLER)) {
            $installer = $this->getCurrentInstaller();
            if ($installer->getId() != $interv->getInstaller()->getId()) {
                throw new AccessDeniedException('operation not allowed');
            }
        }

        $interv->setCloseDate(new \DateTime());
        $form = $this->createFormBuilder($interv, array('validation_groups' => array('close')))
                ->add('closeDate', 'datetime', array('label' => 'Data/ora chiusura', 'required' => true, 'date_widget' => 'single_text'))
                ->getForm()
        ;

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $now = new \DateTime();
                    foreach ($interv->getDetails() as $detail) {
                        /* @var $detail \Boilr\BoilrBundle\Entity\InterventionDetail */
                        $detail->getSystem()->setLastMaintenance($now);
                    }
                    $interv->setStatus(MaintenanceIntervention::STATUS_CLOSED);
                    $this->getEntityManager()->flush();
                    $this->setNoticeMessage('Intervento concluso con successo');

                    return $this->redirect($this->generateUrl('intervention_insert_result', array('id' => $interv->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'interv' => $interv);
    }

    /**
     * Insert results for given intervention
     *
     * @Route("/{id}/choose-detail", name="intervention_insert_result")
     * @Secure(roles="ROLE_INSTALLER")
     * @Template()
     */
    public function insertResultAction()
    {
        $interv = $this->paramConverter('id');

        if ($interv->getHasCheckResults()) {
            $this->setErrorMessage("I risultati dell'intevento sono stati già inseriti.");

            return $this->getLastRoute();
        }

        // if current user is an installer, check if the intervention has been linked to him
        if ($this->getCurrentUser()->hasRole(MyGroup::ROLE_INSTALLER)) {
            $installer = $this->getCurrentInstaller();
            if ($installer->getId() != $interv->getInstaller()->getId()) {
                throw new AccessDeniedException('operation not allowed');
            }
        }

        if ($interv->getDetails()->count() == 1) {
            $details = $interv->getDetails();
            return $this->redirect($this->generateUrl('interventiondetail_insert_result', array('id' => $details[0]->getId())));
        }

        // @todo: restituire una pagina con la lista degli impianti, ognuno con un redirect verso interventiondetail_insert_result
    }

    /**
     * Insert results for given intervention detail
     *
     * @Route("/{id}/insert-results", name="interventiondetail_insert_result")
     * @Secure(roles="ROLE_INSTALLER")
     * @Template()
     */
    public function insertDetailResultsAction()
    {
        $detail = $this->paramConverter('id', "BoilrBundle:InterventionDetail");
        $model = new InterventionDetailResults($detail);
        $form = $this->createForm(new DetailResultsForm(), $model);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $success = $this->getEntityRepository()->persistCheckResults($model);
                if ($success) {
                    $this->setNoticeMessage('Risultati salvati con successo');

                    return $this->redirect($this->generateUrl('intervention_detail_for_installer', array('id' => $detail->getIntervention()->getId())));
                } else {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'detail' => $detail);
    }

    /**
     *
     *
     * @Route("/{id}/choose-template", name="intervention_choose_template")
     * @Template()
     */
    public function chooseTemplateAction()
    {
        $intervention = $this->paramConverter('id');
        $form = $this->createForm(new ChooseTemplateForm());

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $clientData = $form->getClientData();
                $template = $clientData['template'];

                return $this->redirect($this->generateUrl('intervention_generate_report', array('id' => $intervention->getId(), 'tid' => $template->getId())));
            }
        }

        return array('form' => $form->createView(), 'intervention' => $intervention);
    }

    /**
     * Generate report for an intervention using a specific template
     *
     * @Route("/{id}/generate/{tid}", name="intervention_generate_report")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR, ROLE_INSTALLER")
     * @Method("get")
     * @Template()
     */
    public function generateReportAction()
    {
        $intervention = $this->paramConverter('id');
        $template = $this->paramConverter('tid', 'BoilrBundle:Template');
        $document = $this->getEntityRepository()->prepareDocument($intervention, $template);
        $fileName = $template->getName() . ".pdf";
        $params = compact('intervention', 'template', 'document');

        $html = $this->renderView('BoilrBundle:MaintenanceIntervention:generateReport.html.twig', $params);

        return new Response(
                        $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                        200, array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                        )
        );
    }

    /**
     * Export given intervention in XML
     *
     * @Route("/{id}/export-xml", name="intervention_xml_export")
     * @Method("get")
     * @Template()
     */
    public function exportXMLAction()
    {
        $interv = $this->paramConverter("id");
        $xmlDoc = $interv->asXml();
        $filename = sprintf("intervento_%d.xml", $interv->getId());

        return new Response($xmlDoc, 200, array(
                    'Content-Type' => 'application/xml',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"')
        );
    }

    /**
     * Export given intervention in XML
     *
     * @Route("/assignment-wizard", name="intervention_assignment_wizard")
     * @Template()
     */
    public function assignmentWizardAction()
    {
        $interventions = $this->getEntityRepository()->findBy(array('installer' => null));
        $installers = array();

        if (count($interventions) > 0) {
            $_installers = new \Doctrine\Common\Collections\ArrayCollection();

            foreach ($interventions as $interv) {
                $details = $interv->getDetails();
                $systemType = $details[0]->getSystem()->getSystemType();
                foreach ($systemType->getInstallers() as $inst) {
                    if (! $_installers->contains($inst)) {
                        $_installers->add($inst);
                    }
                }
            }


            foreach ($_installers as $inst) {
                $entry['id'] = $inst->getId();
                $entry['name'] = $inst->getFullName();
                $entry['load'] = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->getLoadForInstaller($inst);
                $entry['abilities'] = $inst->getAbilitiesDescr();

                $installers[] = $entry;
            }
        }

        return array('interventions' => $interventions, 'installers' => $installers);
    }

    /**
     * Apply equal balanced policy
     *
     * @Route("/policy/equal", name="intervention_equalpolicy")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function applyEqualPolicyAction()
    {
        $interventions = $this->getEntityRepository()->findBy(array('installer' => null));
        $installers = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->findAll();
        $em = $this->getEntityManager();

        $policy = new \Boilr\BoilrBundle\Policy\EqualBalancedPolicy($em);
        $policy->setInstallers($installers);
        $policy->setInterventions($interventions);

        $results = $policy->elaborate();

        if ($this->getRequest()->get('doit', false)) {
            try {
                foreach ($results as $entry) {
                    $intervention = $entry['intervention'];
                    $installer = $entry['installer']['obj'];
                    $intervention->setInstaller($installer);
                }
                $this->getEntityManager()->flush();
                $this->setNoticeMessage('Interventi assegnati con successo');

                return $this->redirect($this->generateUrl('intervention_assignment_wizard'));
            } catch (Exception $exc) {
                $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
            }
        }

        return array('interventions' => $interventions, 'results' => $results);
    }

}

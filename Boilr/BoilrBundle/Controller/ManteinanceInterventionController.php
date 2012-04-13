<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\UnplannedInterventionForm,
    Boilr\BoilrBundle\Entity\InterventionDetail,
    Boilr\BoilrBundle\Form\ManteinanceInterventionSearchForm,
    Boilr\BoilrBundle\Form\Model\ManteinanceInterventionFilter,
    Boilr\BoilrBundle\Form\ManteinanceInterventionForm,
    Boilr\BoilrBundle\Extension\MyDateTime;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class ManteinanceInterventionController extends BaseController
{
    function __construct()
    {
        $this->entityName = 'BoilrBundle:ManteinanceIntervention';
    }

    /**
     * @Route("/", name="main_intervention")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/current-month", name="current_month_interventions")
     * @Template()
     */
    public function listCurrentMonthAction()
    {
        $now   = new \DateTime();
        $year  = $now->format('Y');
        $month = $now->format('m');

        return $this->redirect( $this->generateUrl('list_all_interventions', array('month' => $month, 'year' => $year)) );
    }

    /**
     * @Route("/list-all/{year}/{month}", name="list_all_interventions")
     * @Template()
     */
    public function listAllAction($year, $month)
    {
        // Build date interval
        $startDate = new \DateTime();
        $endDate   = new \DateTime();
        $startDate->setDate($year, $month, 1);
        $monthName = $startDate->format('F');
        $lastDay = date("d", strtotime("last day of $monthName $year"));
        $endDate->setDate($year, $month, $lastDay);

        // Evaluate next/prev month
        $interval  = \DateInterval::createFromDateString('1 month');
        $nextMonth = new \DateTime();
        $nextMonth->setDate($year, $month, 1);
        $nextMonth->add($interval);
        $prevMonth = new \DateTime();
        $prevMonth->setDate($year, $month, 1);
        $prevMonth->sub($interval);

        // Search interventions
        $records = $this->getEntityRepository()
                        ->interventionsBetweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        // Format titles
        $results = array();
        foreach ($records as $intervention) {
            $date = $intervention->getScheduledDate();
            $day  = $date->format('d');
            $results[$day][] = $this->getInterventionTitle($intervention);
        }

        return array('records' => $results, 'year' => $year, 'month' => $month, 'prevMonth' => $prevMonth, 'nextMonth' => $nextMonth);
    }

    /**
     * @Route("/show/{id}", name="intervention_detail")
     * @ParamConverter("interv", class="BoilrBundle:ManteinanceIntervention")
     * @Template(vars={"interv"})
     */
    public function showAction(ManteinanceIntervention $interv)
    {
    }

    /**
     * Returns HTML representation of this intervention
     *
     * @return string
     */
    protected function getInterventionTitle(ManteinanceIntervention $int)
    {
        $url   = $this->generateUrl('intervention_detail', array('id' => $int->getId()));
        switch ($int->getStatus()) {
            case ManteinanceIntervention::STATUS_TENTATIVE:
                $icon = "ui-icon-help";
                break;
            case ManteinanceIntervention::STATUS_ABORTED:
                $icon = "ui-icon-trash";
                break;
            case ManteinanceIntervention::STATUS_CLOSED:
                $icon = "ui-icon-check";
                break;
            default:
                $icon = "ui-icon-alert";
                break;
        }

        $title = $int->getCustomer()->getSurname();
        $time  = $int->getScheduledDate()->format('H:i');
        $html  = sprintf('<li><a href="%s">%s <span class="ui-icon %s"></span>%s</a></li>',
                         $url, $time, $icon, $title);

        return $html;
    }

    /**
     * @Route("/add-unplanned/{id}", name="unplanned_intervention_add")
     * @ParamConverter("customer", class="BoilrBundle:Person")
     * @Template()
     */
    public function addUnplannedInterventionAction(MyPerson $customer)
    {
        $interv = ManteinanceIntervention::interventionForCustomer($customer);
        $form   = $this->createForm(new ManteinanceInterventionForm(), $interv, array('validation_groups' => array('unplanned')));

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $miRepo   = $this->getDoctrine()->getRepository(self::ENTITY);
                $retValue = $miRepo->persistUnplannedIntervention($interv);

                if ($retValue['success'] === true) {
                    $this->setNoticeMessage('Operazione conclusa con successo');
                    $year  = $interv->getScheduledDate()->format('Y');
                    $month = $interv->getScheduledDate()->format('m');

                    return $this->redirect( $this->generateUrl('list_all_interventions', array('year' => $year, 'month' => $month)));
                } else {
                    $this->setErrorMessage($retValue['message']);
                }
            }
        }

        return array('form' => $form->createView(), 'customer' => $customer);
    }

    /**
     * @Route("/update-unplanned/{id}", name="unplanned_intervention_edit")
     * @ParamConverter("interv", class="BoilrBundle:ManteinanceIntervention")
     * @Template()
     */
    public function updateUnplannedInterventionAction(ManteinanceIntervention $interv)
    {
        $customer = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
        $mi = new ManteinanceIntervention();
        $mi->setCustomer($customer);

        foreach ($customer->getSystems() as $system) {
            $detail = new \Boilr\BoilrBundle\Entity\InterventionDetail();
            $detail->setIntervention($mi);
            $detail->setSystem($system);

            $mi->addInterventionDetail($detail);
        }

        $form = $this->createForm(new \Boilr\BoilrBundle\Form\ManteinanceInterventionForm, $mi);

        return array('form' => $form->createView(), 'customer' => $customer);

        /*
        $opGroups = $this->getDoctrine()->getRepository('BoilrBundle:OperationGroup')->findAll();
        $customer = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
        $mi = new \Boilr\BoilrBundle\Form\Model\UnplannedIntervention($customer, $opGroups);
        $form = $this->createForm(new UnplannedInterventionForm(), $mi);

        return array('form' => $form->createView(), 'customer' => $customer);
        */

        //--------------------------------------------------------------------

        /**
         * If I'm creating a new intervention, a customer must be specified (pid)
         * otherwise I'm trying to edit an existing intervention (iid)
         */
        /*
        if (isset($pid)) {
            $customer = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
            if (! $customer) {
                throw new NotFoundHttpException("Invalid customer");
            }

            // Check if selected customer has at least one system, otherwise redirect to his profile page
            if ($customer->getSystems()->count() == 0) {
                $this->setErrorMessage('Non è stato associato alcun impianto al cliente.');

                return $this->redirect( $this->generateUrl('show_person', array('id' => $customer->getId() )));
            }

            $interv = new \Boilr\BoilrBundle\Form\Model\UnplannedIntervention($customer);
            $aDate = MyDateTime::nextWorkingDay(new \DateTime() );
            $aDate->setTime(8, 0, 0);
            $interv->setScheduledDate($aDate);
        } else {
            // An update has been requested, fetch the intervention from the store
            $interv = $this->getDoctrine()->getRepository(self::ENTITY)->findOneById($iid);
            if (! $interv) {
                throw new NotFoundHttpException("Invalid intervention");
            }
            $customer = $interv->getCustomer();
        }

        // Build the form
        $form = $this->createForm(new UnplannedInterventionForm(), $interv);
                                  // array('validation_groups' => array('unplanned')) );

        // Check if user submitted the form
        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                // ladybug_dump($interv); die();
                $miRepo = $this->getDoctrine()->getRepository(self::ENTITY);

                // further check: verify that given intervention doesn't overlap with any other
                $overlaps = $miRepo->doesInterventionOverlaps( $interv->getScheduledDate() );
                if ($overlaps) {
                    $this->setErrorMessage("La data/ora richiesta si sovrappone con un altro appuntamento.");
                } else {
                    $mi = $interv->factory();
                    // evaluate expected close date
                    $miRepo->evalExpectedCloseDate($mi);

                    // try to persist changes to the store
                    $success = true;

                    try {
                        $em = $this->getEntityManager();
                        $em->persist($mi);
                        $em->flush();
                    } catch (\PDOException $exc) {
                        var_dump($exc->getMessage());
                        $success = false;
                    }

                    if ($success) {
                        $this->setNoticeMessage('Operazione creata con successo');
                        $year  = $mi->getScheduledDate()->format('Y');
                        $month = $mi->getScheduledDate()->format('m');

                        return $this->redirect( $this->generateUrl('list_all_interventions', array('year' => $year, 'month' => $month)));
                    } else {
                        $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                    }
                }
            }
        }

        return array('form' => $form->createView(), 'customer' => $customer);
        */
    }

    /**
     * @Route("/set-installer/{id}", name="add_installer_to_interv")
     * @ParamConverter("interv", class="BoilrBundle:ManteinanceIntervention")
     * @Template()
     */
    public function addInstallerAction(ManteinanceIntervention $interv)
    {
        // Build the flow/form
        $flow = $this->get('boilr.form.flow.linkInstaller');

        // reset data if it's first time I request the page
        if ($this->getRequest()->getMethod() === 'GET') {
            $flow->reset();
        }

        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($interv);

        $form = $flow->createForm($interv);
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData();

            if ($flow->nextStep()) {
                return array(
                    'form'   => $flow->createForm($interv)->createView(),
                    'flow'   => $flow,
                    'interv' => $interv
                );
            }

            // flow finished
            try {
                $em = $this->getEntityManager();
                $em->persist($interv);
                $em->flush();
                $flow->reset();
                $this->setNoticeMessage("Operazione completata con successo");

                return $this->redirect($this->generateUrl('intervention_detail', array('id' => $interv->getId() )));
            } catch (\PDOException $exc) {
                $this->setErrorMessage("Si è verificato un'errore durante il salvataggio");
            }
        }

        return array('form' => $form->createView(), 'flow' => $flow, 'interv' => $interv );
    }

    /**
     * @Route("/search", name="search_intervention")
     * @Template
     */
    public function searchAction()
    {
        $filter  = new ManteinanceInterventionFilter();
        $form    = $this->createForm(new ManteinanceInterventionSearchForm, $filter);
        $results = array();

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $results = $this->getEntityManager()->getRepository(self::ENTITY)->searchInterventions($filter);
            }
        }

        return array('form' => $form->createView(), 'results' => $results);
    }

    /**
     * Cancel given intervention
     *
     * @Route("/abort/{id}", name="intervention_abort")
     * @ParamConverter("interv", class="BoilrBundle:ManteinanceIntervention")
     * @Template()
     */
    public function abortAction(ManteinanceIntervention $interv)
    {
        try {
            $interv->setStatus(ManteinanceIntervention::STATUS_ABORTED);
            $this->getEntityManager()->flush();
            $this->setNoticeMessage('Intervento annullato con successo');
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un problema durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('intervention_detail', array('id' => $interv->getId() )));
    }
}

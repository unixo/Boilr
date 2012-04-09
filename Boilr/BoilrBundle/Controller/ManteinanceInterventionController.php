<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\UnplannedInterventionForm,
    Boilr\BoilrBundle\Form\ManteinanceInterventionSearchForm,
    Boilr\BoilrBundle\Form\Model\ManteinanceInterventionFilter,
    Boilr\BoilrBundle\Extension\MyDateTime;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class ManteinanceInterventionController extends BaseController
{
    const ENTITY = 'BoilrBundle:ManteinanceIntervention';

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
        $records = $this->getDoctrine()->getRepository(self::ENTITY)
                        ->interventionsBetweenDates($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        // Format titles
        $results = array();
        foreach ($records as $intervention) {
            $date = $intervention->getOriginalDate();
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
            default:
                break;
        }
        $title = $int->getCustomer()->getSurname();

        /*
        $html = sprintf('<span class="event"><a href="%s"><span class="ui-icon %s"></span>%s</a></span>',
                $url, $icon, $title);
         */
        $time = $int->getOriginalDate()->format('H:i');
        $html = sprintf('<li><a href="%s">%s <span class="ui-icon %s"></span>%s</a></li>',
                $url, $time, $icon, $title);

        return $html;
    }

    /**
     * @Route("/add-unplanned/{pid}", name="unplanned_intervention_add")
     * @Route("/update-unplanned/{iid}", name="unplanned_intervention_edit")
     * @Template()
     */
    public function addOrUpdateUnplannedInterventionAction($pid = null, $iid = null)
    {
        $interv   = null;
        /* @var $interv \Boilr\BoilrBundle\Entity\ManteinanceIntervention */
        $customer = null;
        /* @var $customer \Boilr\BoilrBundle\Entity\Person */

        /**
         * If I'm creating a new intervention, a customer must be specified (pid)
         * otherwise I'm trying to edit an existing intervention (iid)
         */
        if (isset($pid)) {
            $customer = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
            if (! $customer) {
                throw new NotFoundHttpException("Invalid customer");
            }

            // Check if selected customer has at least one system, otherwise redirect to his profile page
            if ($customer->getSystems()->count() == 0) {
                $this->setErrorMessage('Non è stato associato alcun impianto alla persona.');

                return $this->redirect( $this->generateUrl('show_person', array('id' => $customer->getId() )));
            }

            $interv = ManteinanceIntervention::UnplannedInterventionFactory();
            $interv->setCustomer($customer);
            $aDate = MyDateTime::nextWorkingDay(new \DateTime() );
            $aDate->setTime(8, 0, 0);
            $interv->setOriginalDate($aDate);
        } else {
            // An update has been requested, fetch the intervention from the store
            $interv = $this->getDoctrine()->getRepository(self::ENTITY)->findOneById($iid);
            if (! $interv) {
                throw new NotFoundHttpException("Invalid intervention");
            }
            $customer = $interv->getCustomer();
        }

        // Build the form
        $form = $this->createForm(new UnplannedInterventionForm(), $interv,
                                  array('validation_groups' => array('unplanned')) );

        // Check if user submitted the form
        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $miRepo = $this->getDoctrine()->getRepository(self::ENTITY);

                // further check: verify that given intervention doesn't overlap with any other
                $overlaps = $miRepo->doesInterventionOverlaps($interv);
                if ($overlaps) {
                    $this->setErrorMessage("La data/ora richiesta si sovrappone con un altro appuntamento.");
                } else {
                    // evaluate expected close date
                    $miRepo->evalExpectedCloseDate($interv);

                    // If system is not linked with any address, update with user selection
                    if ($interv->getSystem()->getAddress() === null) {
                    $system = $interv->getSystem();
                    $system->setAddress($interv->getAddress());
                    }

                    // try to persist changes to the store
                    $success = true;

                    try {
                        $em = $this->getEntityManager();
                        $em->persist($interv);
                        $em->flush();
                    } catch (\PDOException $exc) {
                        var_dump($exc->getMessage());
                        $success = false;
                    }

                    if ($success) {
                        $this->setNoticeMessage('Operazione creata con successo');
                        $year  = $interv->getOriginalDate()->format('Y');
                        $month = $interv->getOriginalDate()->format('m');

                        return $this->redirect( $this->generateUrl('list_all_interventions', array('year' => $year, 'month' => $month)));
                    } else {
                        $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                    }
                }
            }
        }

        return array('form' => $form->createView(), 'customer' => $customer);
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

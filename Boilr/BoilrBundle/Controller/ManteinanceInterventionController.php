<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\UnplannedInterventionForm,
    Boilr\BoilrBundle\Form\PersonPickerForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class ManteinanceInterventionController extends BaseController
{
    /**
     * @Route("/", name="main_intervention")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/list-all/{year}/{month}", name="list_all_interventions")
     * @Template()
     */
    public function listAllAction($year, $month)
    {
        $monthName = date("F", strtotime("01-$month-1970"));
        $date1 = date('Y-m-d', strtotime("first day of $monthName $year"));
        $date2 = date('Y-m-d', strtotime("last day of $monthName $year"));

        $records = $this->getEntityManager()->createQuery(
                            "SELECT si FROM BoilrBundle:ManteinanceIntervention si ".
                            "WHERE si.originalDate >= :date1 AND si.originalDate <= :date2 ".
                            "ORDER BY si.originalDate")
                        ->setParameters(array('date1' => $date1, 'date2' => $date2))
                        ->getResult();

        $results = array();
        foreach ($records as $intervention) {
            $date = $intervention->getOriginalDate();
            $day  = $date->format('d');
            $results[$day][] = $this->getInterventionTitle($intervention);
        }

        return array('records' => $results, 'year' => $year, 'month' => $month);
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
        $icon  = ($int->getStatus()==ManteinanceIntervention::STATUS_TENTATIVE?"ui-icon-help":"ui-icon-check");
        $title = $int->getCustomer()->getSurname();

        $html = sprintf('<span class="event"><a href="%s"><span class="ui-icon %s"></span>%s</a></span>',
                $url, $icon, $title);

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
        } else {
            $interv = $this->getDoctrine()->getRepository('BoilrBundle:ManteinanceIntervention')->findOneById($iid);
            if (! $interv) {
                throw new NotFoundHttpException("Invalid intervention");
            }
            $customer = $interv->getCustomer();
        }

        // Build the form
        $repository = $this->getEntityManager()->getRepository('BoilrBundle:System');
        $form       = $this->createForm(new UnplannedInterventionForm($repository), $interv,
                                        array('validation_groups' => array('unplanned')) );

        // Check if user submitted the form
        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
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

        return array('form' => $form->createView(), 'customer' => $customer);
    }

    /**
     * @Route("/set-installer/{id}", name="add_installer_to_interv")
     * @ParamConverter("interv", class="BoilrBundle:ManteinanceIntervention")
     * @Template()
     */
    public function addInstallerAction(ManteinanceIntervention $interv)
    {
        $form = $this->createForm(new PersonPickerForm());

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $installer = $form->getClientData();
                die();
            }
        }

        return array('form' => $form->createView(), 'interv' => $interv);
    }
}

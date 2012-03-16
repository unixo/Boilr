<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\ManteinanceIntervention,
    Boilr\BoilrBundle\Form\UnplannedInterventionForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
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
            $results[$day][] = $intervention->getCustomer()->getSurname();
        }

        return array('records' => $results, 'year' => $year, 'month' => $month);
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
}

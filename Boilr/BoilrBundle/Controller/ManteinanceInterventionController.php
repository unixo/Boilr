<?php

namespace Boilr\BoilrBundle\Controller;

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

        return array('records' => $results);
    }
}

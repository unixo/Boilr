<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Address as MyAddress;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Symfony\Component\HttpFoundation\Response;

class PersonController extends BaseController
{
    /**
     * @Route("/", name="main_person")
     * @Template
     */
    public function mainAction()
    {
        return array();
    }

    /**
     * @Route("/new", name="new_person")
     * @Template
     */
    public function newAction()
    {
        $newPerson = new MyPerson();
        $flow      = $this->get('boilr.form.flow.newperson');

        // reset data if it's first time I request the page
        if ($this->getRequest()->getMethod() === 'GET') {
            $flow->reset();
        }

        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($newPerson);

        $form = $flow->createForm($newPerson);
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData();

            if ($flow->nextStep()) {
                return array(
                    'form'   => $flow->createForm($newPerson)->createView(),
                    'flow'   => $flow,
                    'person' => $newPerson
                );
            }

            // flow finished
            $success = $this->getDoctrine()->getRepository('BoilrBundle:Person')->persistPerson($newPerson);
            if ($success) {
                $flow->reset();
                $this->setFlashMessage(self::FLASH_NOTICE, "Operazione completata con successo");

                return $this->redirect($this->generateUrl('show_person', array('id' => $newPerson->getId() )));
            } else {
                $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un'errore durante il salvataggio");
            }
        }

        return array('form'   => $form->createView(),
                     'flow'   => $flow,
                     'person' => $newPerson );
    }

    /**
     * @Route("/search", name="search_person")
     * @Template("BoilrBundle:Person:search.html.twig")
     */
    public function searchAction()
    {
        return array();
    }

    /**
     * @Route("/json-search", name="json_search_person")
     */
    public function jsonListAction()
    {
        $request        = $this->getRequest();
        $sEcho          = (int) $request->get('sEcho');
        $iDisplayStart  = $request->get('iDisplayStart');
        $iDisplayLength = 30; //$request->get('iDisplayLength');
        $sSearch        = $request->get('sSearch');

        $sWhere = '';
        if ($sSearch)
            $sWhere = "WHERE surname LIKE '%$sSearch%'";

        $iTotalRecords  = $this->getDoctrine()->getEntityManager()
                               ->createQuery('SELECT COUNT(p.id) FROM BoilrBundle:Person p')
                               ->getSingleScalarResult();

        $query          = $this->getDoctrine()->getRepository('BoilrBundle:Person')
                               ->createQueryBuilder('p')->select('p')
                               ->setFirstResult($iDisplayStart)
                               ->setMaxResults($iDisplayLength)
                               ->orderBy('p.surname, p.name');

        if ($request->get('is_customer') != null)
            $query->andWhere('p.isCustomer = 0');

        if ($request->get('is_customer') != null)
            $query->andWhere('p.isCustomer = 0');

        if ($request->get('is_admin') != null)
            $query->andWhere('p.isAdministrator = 0');

        if (strlen($sSearch))
            $query->andWhere('p.name LIKE :term or p.surname LIKE :term')
                  ->setParameter('term', '%'.$sSearch.'%');

        $results = $query->getQuery()->getResult();

        if (strlen($sSearch)) {
            $conn = $this->get('database_connection');
            /* @var $iTotalRecords \Doctrine\DBAL\Driver\PDOConnection */
            $result = $conn->query('SELECT FOUND_ROWS()');
            $iTotalDisplayRecords = $result->fetchColumn();
        } else
            $iTotalDisplayRecords = $iTotalRecords;

        $data = array('aaData'               => array(),
                      'sEcho'                => $sEcho,
                      'iTotalRecords'        => $iTotalRecords,
                      'iTotalDisplayRecords' => $iTotalDisplayRecords);

        foreach ($results as $p)
            $data['aaData'][] = array(
                    'id'           => $p->getId(),
                    'type'         => $p->getType(),
                    'fullname'     => $p->getSurname() . ' '. $p->getName(),
                    'is_installer' => $p->getIsInstaller(),
                    'is_admin'     => $p->getIsAdministrator(),
                    'is_supplier'  => $p->getIsSupplier(),
                    'is_customer'  => $p->getIsCustomer()
                );


        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/show/{id}", name="show_person")
     * @ParamConverter("person", class="BoilrBundle:Person")
     * @Template("BoilrBundle:Person:show.html.twig", vars={"person"})
     */
    public function showAction(MyPerson $person)
    {
    }

    /**
     * @Route("/json-details/{id}", name="json_person_details")
     * @ParamConverter("person", class="BoilrBundle:Person")
     */
    public function jsonPersonDetailAction(MyPerson $person)
    {
        $data = array(
                'type' => $person->getType(),
                'name' => $person->getName(),
                'surname' => $person->getSurname(),
                'fiscalCode' => $person->getFiscalCode(),
                'vatCode' => $person->getVatCode(),
                'isInstaller' => $person->getIsInstaller(),
                'isCustomer' => $person->getIsCustomer(),
                'isAdministrator' => $person->getIsAdministrator(),
                'homePhone' => $person->getHomePhone(),
                'officePhone' => $person->getOfficePhone(),
                'cellularPhone' => $person->getCellularPhone(),
                'faxNumber' => $person->getFaxNumber(),
                'email1' => $person->getPrimaryMail(),
                'email2' => $person->getSecondaryMail()
        );

        foreach ($person->getAddresses() as $address) {
            /* @var $address \Boilr\BoilrBundle\Entity\Address */
            $data['addresses'][] = array(
              'street' => $address->getStreet(),
              'city' => $address->getCity(),
              'postalCode' => $address->getPostalCode()
            );
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
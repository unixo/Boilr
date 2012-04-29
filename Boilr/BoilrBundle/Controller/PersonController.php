<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person,
    Boilr\BoilrBundle\Entity\Address as MyAddress,
    Boilr\BoilrBundle\Form\PersonRegistryForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Symfony\Component\HttpFoundation\Response,
    JMS\SecurityExtraBundle\Annotation\Secure;

class PersonController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Person';
    }

    /**
     * @Route("/", name="main_person")
     * @Template
     */
    public function mainAction()
    {
        return array();
    }

    /**
     * @Route("/new-contact", name="new_person")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template
     */
    public function newAction()
    {
        // Build the flow/form
        $newPerson = new Person();
        $flow = $this->get('boilr.form.flow.newperson');

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
                    'form' => $flow->createForm($newPerson)->createView(),
                    'flow' => $flow,
                    'person' => $newPerson
                );
            }

            // flow finished
            $success = $this->getEntityRepository()->persistPerson($newPerson);
            if ($success) {
                $flow->reset();
                $this->setNoticeMessage("Operazione completata con successo");

                return $this->redirect($this->generateUrl(
                                        'show_person', array('id' => $newPerson->getId())));
            } else {
                $this->setErrorMessage("Si è verificato un'errore durante il salvataggio");
            }
        }

        return array('form' => $form->createView(), 'flow' => $flow, 'person' => $newPerson);
    }

    /**
     * @Route("/search", name="search_person")
     * @Template()
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
        $request = $this->getRequest();
        $sEcho = (int) $request->get('sEcho');
        $iDisplayStart = $request->get('iDisplayStart');
        $iDisplayLength = 30; //$request->get('iDisplayLength');
        $sSearch = $request->get('sSearch');

        $sWhere = '';
        if ($sSearch) {
            $sWhere = "WHERE surname LIKE '%$sSearch%'";
        }

        $iTotalRecords = $this->getDoctrine()->getEntityManager()
                ->createQuery('SELECT COUNT(p.id) FROM BoilrBundle:Person p')
                ->getSingleScalarResult();

        $query = $this->getEntityRepository()
                ->createQueryBuilder('p')->select('p')
                ->setFirstResult($iDisplayStart)
                ->setMaxResults($iDisplayLength)
                ->orderBy('p.surname, p.name');

        if (strlen($sSearch)) {
            $query->andWhere('p.name LIKE :term or p.surname LIKE :term')
                    ->setParameter('term', '%' . $sSearch . '%');
        }

        $this->_log($query->getDQL());

        $results = $query->getQuery()->getResult();

        if (strlen($sSearch)) {
            $conn = $this->get('database_connection');
            /* @var $iTotalRecords \Doctrine\DBAL\Driver\PDOConnection */
            $result = $conn->query('SELECT FOUND_ROWS()');
            $iTotalDisplayRecords = $result->fetchColumn();
        } else {
            $iTotalDisplayRecords = $iTotalRecords;
        }

        $data = array('aaData' => array(),
            'sEcho' => $sEcho,
            'iTotalRecords' => $iTotalRecords,
            'iTotalDisplayRecords' => $iTotalDisplayRecords);

        foreach ($results as $p) {
            /* @var $p \Boilr\BoilrBundle\Entity\Person */
            $data['aaData'][] = array(
                'id' => $p->getId(),
                'fullname' => $p->getSurname() . ' ' . $p->getName(),
                'homePhone' => $p->getHomePhone(),
                'officePhone' => $p->getOfficePhone(),
                'systems' => $p->getSystems()->count(),
            );
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Returns all person properties formatted in json
     *
     * @Route("/json-get-person-detail", name="json_get_person")
     */
    public function jsonGetPersonDetailAction()
    {
        $data = array();
        $pid = $this->getRequest()->get('pid');
        $person = $this->getEntityRepository()->findOneById($pid);
        /* @var $person \Boilr\BoilrBundle\Entity\Person */

        if ($person) {
            $reflect = new \ReflectionClass($person);
            $props = $reflect->getProperties();

            foreach ($props as $prop) {
                $name = $prop->getName();
                $method = 'get' . ucfirst($name);
                $value = $person->$method();
                if (!is_object($value)) {
                    $data[$name] = $value;
                }
            }
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/show/{id}", name="show_person")
     * @Template()
     */
    public function showAction()
    {
        $person = $this->paramConverter("id");
        $interventions = $this->getDoctrine()->getRepository('BoilrBundle:ManteinanceIntervention')
                ->interventionsForCustomer($person);

        return array('person' => $person, 'interventions' => $interventions);
    }

    /**
     * @Route("/{id}/update-registry", name="person_registry_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function updateRegistryAction()
    {
        $person = $this->paramConverter("id");
        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(
                new PersonRegistryForm(), $person, array('validation_groups' => array('registry'))
        );

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    $dem->flush();
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect($this->generateUrl('show_person', array('id' => $person->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person);
    }

}
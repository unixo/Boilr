<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;
use Boilr\BoilrBundle\Entity\Company,
    Boilr\BoilrBundle\Form\CompanyForm;

/**
 * Description of CompanyController
 *
 * @author unixo
 */
class CompanyController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Company';
    }

    /**
     * @Route("/", name="company_list")
     * @Template()
     */
    public function listAction()
    {
        $companies = $this->getEntityRepository()->findBy(array(), array('name' => 'ASC'));

        return array('companies' => $companies);
    }

    /**
     * @Route("/{id}/list-employees", name="company_employee_list")
     * @Template()
     */
    public function listEmployeesAction()
    {
        $company = $this->paramConverter("id");
        $employees = $this->getEntityRepository()->getEmployees($company);

        return array('company' => $company, 'employees' => $employees);
    }

    /**
     * @Route("/{id}/delete", name="company_delete")
     * @Template()
     */
    public function deleteAction()
    {
        $company = $this->paramConverter("id");
        try {
            $dem = $this->getEntityManager();
            $dem->remove($company);
            $dem->flush();
            $this->setNoticeMessage("Impianto eliminato con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
        }

        return $this->getLastRoute();
    }

    /**
     * @Route("/add", name="company_add")
     * @Route("/{cid}/update", name="company_edit")
     * @Template()
     */
    public function addOrUpdateAction($cid = null)
    {
        $company = null;

        if ($cid === null) {
            $company = new Company();
        } else {
            $company = $this->getEntityRepository()->findOneById($cid);
            if (!$company) {
                throw new \InvalidArgumentException("Invalid argument");
            }
        }

        $form = $this->createForm(new CompanyForm(), $company);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($cid == null) {
                        $dem->persist($company);
                    }
                    $dem->flush();
                    $this->setNoticeMessage('Operazione conclusa con successo');

                    return $this->redirect($this->generateUrl('company_list'));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView());
    }

}
<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Boilr\BoilrBundle\Entity\ContractType as MyContractType,
    Boilr\BoilrBundle\Form\ContractTypeType;

class ContractTypeController extends BaseController
{
    /**
     * @Route("/new", name="new_contract_type")
     * @Template()
     */
    public function newAction()
    {
        $ct      = new MyContractType();
        $form    = $this->createForm(new ContractTypeType(), $ct);
        $request = $this->getRequest();

        $form = $this->createFormBuilder()->add('code', 'text', array('required' => true));


        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($ct);
                    $em->flush();

                    $this->getSession()->setFlash('notice', 'La tipologia è stata creata con successo!');

                    return $this->redirect( $this->generateUrl('list_contract_type') );
                } catch (\PDOException $exc) {
                    $this->getSession()->setFlash('error', 'Impossibile completare l\'operazione');
                }
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/list", name="list_contract_type")
     * @Template()
     */
    public function listAction()
    {
        $records = $this->getDoctrine()->getRepository('BoilrBundle:ContractType')
                        ->findAll();

        return array('records' => $records);
    }

    /**
     * @Route("/delete/{id}", name="delete_contract_type")
     * @ParamConverter("ctype", class="BoilrBundle:ContractType")
     * @Template()
     */
    public function deleteAction(MyContractType $ctype)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($ctype);
        $em->flush();

        return $this->redirect( $this->generateUrl('list_contract_type') );
    }
}

<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Contract as MyContract,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Form\ContractForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class ContractController extends BaseController
{
    /**
     * @Route("/add/{id}", name="add_contract")
     * @ParamConverter("system", class="BoilrBundle:System")
     * @Template()
     */
    public function addAction(MySystem $system)
    {
        if (! $system) {
            throw new NotFoundHttpException("Invalid parameter");
        }

        $customer = $system->getOwner();
        $contract = new MyContract();
        $form     = $this->createForm(new ContractForm(), $contract);
        $contract->setCustomer($customer);
        $contract->setSystem($system);

        if ($this->isPOSTRequest()) {
            $form->bindRequest( $this->getRequest() );

            if ($form->isValid()) {
                $success = $this->getDoctrine()->getRepository('BoilrBundle:Contract')
                                ->createNewContract($contract);
                
                if ($success) {
                    $this->setFlashMessage(self::FLASH_NOTICE, 'Operazione completata con successo');

                    return $this->redirect( $this->generateUrl('show_person', array('id' => $customer->getId() )));
                } else {
                    $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un'errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'system' => $system);
    }
}

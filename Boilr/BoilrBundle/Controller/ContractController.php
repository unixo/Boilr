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
        if (! $system->getAddress()) {
            if ($system->getOwner()->getAddresses()->count() > 0) {
                $this->setErrorMessage("L'impianto non è associato ad alcun indirizzo: selezionarlo adesso.");

                return $this->redirect( $this->generateUrl('system_edit', array('sid' => $system->getId())) );
            } else {
                $this->setErrorMessage("L'impianto non è associato ad alcun indirizzo: specificare l'indirizzo.");

                return $this->redirect( $this->generateUrl('address_add', array('pid' => $system->getOwner()->getId())) );
            }
        }

        $customer = $system->getOwner();
        $contract = new MyContract();
        $form     = $this->createForm(new ContractForm(), $contract);
        $contract->setCustomer($customer);
        $contract->setSystem($system);

        if ($this->isPOSTRequest()) {
            $form->bindRequest( $this->getRequest() );

            if ($form->isValid()) {
                $repo = $this->getDoctrine()->getRepository('BoilrBundle:Contract');

                // Check if this contract overlaps other active contracts
                if ($repo->isContractLegal($contract)) {
                    // Persist contract to store
                    $success = $repo->createNewContract($contract);

                    // Display flash message to the user
                    if ($success) {
                        $this->setFlashMessage(self::FLASH_NOTICE, 'Operazione completata con successo');

                        return $this->redirect( $this->generateUrl('show_person', array('id' => $customer->getId() )));
                    } else {
                        $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un'errore durante il salvataggio");
                    }
                } else {
                    $this->setFlashMessage(self::FLASH_ERROR, "I dati inseriti contrastano con altri contratti");
                }
            }
        }

        return array('form' => $form->createView(), 'system' => $system);
    }
}

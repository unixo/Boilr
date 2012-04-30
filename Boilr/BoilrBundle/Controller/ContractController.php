<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Contract as MyContract,
    Boilr\BoilrBundle\Entity\System as MySystem,
    Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Attachment as MyAttachment,
    Boilr\BoilrBundle\Form\ContractForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

class ContractController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Contract';
    }

    /**
     * @Route("/list", name="contract_list")
     * @Template()
     */
    public function listAction()
    {
        $contracts = $this->getEntityRepository()->findBy(array(), array('id' => 'ASC'));

        return array('contracts' => $contracts);
    }

    /**
     * @Route("/add/{id}", name="add_contract")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function addAction()
    {
        $system = $this->paramConverter("id", "BoilrBundle:System");
        if (!$system->getAddress()) {
            if ($system->getOwner()->getAddresses()->count() > 0) {
                $this->setErrorMessage("L'impianto non è associato ad alcun indirizzo: selezionarlo adesso.");

                return $this->redirect($this->generateUrl('system_edit', array('sid' => $system->getId())));
            } else {
                $this->setErrorMessage("L'impianto non è associato ad alcun indirizzo: specificare l'indirizzo.");

                return $this->redirect($this->generateUrl('address_add', array('pid' => $system->getOwner()->getId())));
            }
        }

        $customer = $system->getOwner();
        $contract = new MyContract();
        $form = $this->createForm(new ContractForm(), $contract);
        $contract->setCustomer($customer);
        $contract->setSystem($system);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $repo = $this->getDoctrine()->getRepository('BoilrBundle:Contract');

                // Check if this contract overlaps other active contracts
                if ($repo->isContractLegal($contract)) {
                    // Persist contract to store
                    $success = $repo->createNewContract($contract);

                    // Display flash message to the user
                    if ($success) {
                        $this->setNoticeMessage('Operazione completata con successo');

                        return $this->redirect($this->generateUrl('show_person', array('id' => $customer->getId())));
                    } else {
                        $this->setErrorMessage("Si è verificato un'errore durante il salvataggio");
                    }
                } else {
                    $this->setErrorMessage("I dati inseriti contrastano con altri contratti");
                }
            }
        }

        return array('form' => $form->createView(), 'system' => $system);
    }

}

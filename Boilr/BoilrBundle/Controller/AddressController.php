<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Address as MyAddress,
    Boilr\BoilrBundle\Form\AddressForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class AddressController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:Address';
    }

    /**
     * @Route("/delete/{id}", name="address_delete")
     * @ParamConverter("address", class="BoilrBundle:Address")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function deleteAction(MyAddress $address)
    {
        $person = $address->getPerson();
        $success = false;

        try {
            $em = $this->getEntityManager();
            $person->getAddresses()->removeElement($address);
            $em->remove($address);
            $em->flush();
            $success = true;
        } catch (\PDOException $exc) {
            $success = false;
        }

        if ($success) {
            $this->setNoticeMessage("Indirizzo eliminato con successo");
        } else {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
        }

        return $this->getLastRoute();
    }

    /**
     * @Route("/add/{pid}", name="address_add")
     * @Route("/update/{aid}", name="address_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function addOrUpdateAction($pid = null, $aid = null)
    {
        $opType = null;
        $person = null;
        /* @var $person MyPerson */

        if (isset($aid)) {
            $opType = 'add';
            $address = $this->getEntityRepository()->findOneById($aid);
            if (!$address) {
                throw new \InvalidArgumentException("Invalid argument");
            } else {
                $person = $address->getPerson();
            }
        } else {
            $opType = 'update';
            $person = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
            if (!$person) {
                throw new \InvalidArgumentException("Invalid argument");
            }
            $address = new MyAddress();
            $address->setPerson($person);
            $person->getAddresses()->add($address);
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new AddressForm(), $address, array('validation_groups' => array('address')));

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $em = $this->getEntityManager();
                    $em->flush();
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect($this->generateUrl('show_person', array('id' => $person->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person, 'opType' => $opType);
    }

}

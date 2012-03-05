<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Person as MyPerson,
    Boilr\BoilrBundle\Entity\Address as MyAddress,
    Boilr\BoilrBundle\Form\AddressForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AddressController extends BaseController
{
    /**
     * @Route("/delete/{id}", name="address_delete")
     * @ParamConverter("address", class="BoilrBundle:Address")
     * @Template()
     */
    public function deleteAction(MyAddress $address)
    {
        if (! $address) {
            throw new NotFoundHttpException("Invalid address");
        }

        $person  = $address->getPerson();
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
            $this->setFlashMessage(self::FLASH_NOTICE, "Indirizzo eliminato con successo");
        } else {
            $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un errore durante l'eliminazione.");
        }

        return $this->redirect( $this->generateUrl('show_person', array('id' => $person->getId())) );
    }

    /**
     * @Route("/add/{pid}", name="address_add")
     * @Route("/update/{aid}", name="address_edit")
     * @Template()
     */
    public function addOrUpdateAction($pid = null, $aid = null)
    {
        $person = null;
        /* @var $person MyPerson */

        if (isset($aid)) {
            $address = $this->getDoctrine()->getRepository('BoilrBundle:Address')
                            ->findOneById($aid);
            if (!$address) {
                throw new NotFoundHttpException("Invalid address");
            } else {
                $person = $address->getPerson();
            }
        } else {
            $person = $this->getDoctrine()->getRepository('BoilrBundle:Person')->findOneById($pid);
            if (! $person) {
                throw new NotFoundHttpException("Invalid person");
            }
            $address = new MyAddress();
            $address->setPerson($person);
            $person->getAddresses()->add($address);
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new AddressForm(), $address,
                            array( 'validation_groups' => array('address') ));

        if ($this->isPOSTRequest()) {
            $form->bindRequest( $this->getRequest() );

            if ($form->isValid()) {
                try {
                    $em = $this->getEntityManager();
                    $em->flush();
                    $this->setFlashMessage(self::FLASH_NOTICE, 'Operazione completata con successo');

                    return $this->redirect( $this->generateUrl('show_person', array('id' => $person->getId() )));
                } catch (Exception $exc) {
                    $this->setFlashMessage(self::FLASH_ERROR, "Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'person' => $person);
    }
}

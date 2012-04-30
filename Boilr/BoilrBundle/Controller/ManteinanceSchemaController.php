<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\ManteinanceSchema,
    Boilr\BoilrBundle\Form\ManteinanceSchemaForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
 */
class ManteinanceSchemaController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:ManteinanceSchema';
    }

    /**
     * @Route("/", name="manteinance_schema_list")
     * @Template()
     */
    public function indexAction()
    {
        $schemas = $this->getEntityManager()->createQuery(
                        "SELECT s FROM BoilrBundle:ManteinanceSchema s " .
                        "JOIN s.systemType st " .
                        "ORDER BY st.id, s.listOrder"
                )->getResult();

        return array('schemas' => $schemas);
    }

    /**
     * @Route("/add", name="manteinance_schema_add")
     * @Route("/update/{id}", name="manteinance_schema_edit")
     * @Template()
     */
    public function addOrUpdateAction($id = null)
    {
        $schema = null;
        /* @var $schema ManteinanceSchema */

        if (isset($id)) {
            if (!($schema = $this->getEntityRepository()->findOneById($id))) {
                throw new \InvalidArgumentException("Invalid argument");
            }
        } else {
            $schema = new ManteinanceSchema();
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new ManteinanceSchemaForm(), $schema, array('validation_groups' => array('schema')));

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $success = $this->getEntityRepository()->persistSchema($schema);
                if ($success) {
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect($this->generateUrl('manteinance_schema_list'));
                } else {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/move/{id}/{dir}", name="manteinance_schema_move")
     * @Template()
     */
    public function moveAction()
    {
        $schema = $this->paramConverter("id");
        $dir = $this->getRequest()->get('dir');
        $dir = $dir?strtolower($dir):"up";
        $_dir = strtolower($dir);
        if (!in_array($_dir, array('up', 'down'))) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $schemas = $this->getEntityRepository()->findBySystemType($schema->getSystemType()->getId());

        $pos = -1;
        for ($i = 0; $i < count($schemas); $i++) {
            $item = $schemas[$i];
            if ($item->getId() == $schema->getId()) {
                $pos = $i;
                break;
            }
        }

        if (count($schemas) > 0) {
            // Move item down
            if ($pos == 0 && $_dir === "down")
                ;
                // @todo da terminare
        }

        return $this->redirect($this->generateUrl('manteinance_schema_list'));
    }

}

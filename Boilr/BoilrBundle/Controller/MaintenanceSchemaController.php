<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\MaintenanceSchema,
    Boilr\BoilrBundle\Form\MaintenanceSchemaForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
 */
class MaintenanceSchemaController extends BaseController
{

    function __construct()
    {
        $this->entityName = 'BoilrBundle:MaintenanceSchema';
    }

    /**
     * @Route("/", name="manteinance_schema_list")
     * @Template()
     */
    public function indexAction()
    {
        $types = $this->getDoctrine()->getRepository('BoilrBundle:SystemType')
                ->findBy(array(), array('name' => 'ASC'));

        return compact('types');
    }

    /**
     * @Route("/{id}/schema-detail", name="manteinance_schema_detail")
     * @Template()
     */
    public function schemaDetailAction()
    {
        $systemType = $this->paramConverter("id", "BoilrBundle:SystemType");
        $count = $systemType->getSchemas()->count();

        return compact('systemType', 'count');
    }

    /**
     * @Route("/{id}/detail", name="manteinance_schema_delete")
     * @Template()
     */
    public function deleteAction()
    {
        try {
            $schema = $this->paramConverter("id");
            $sysType = $schema->getSystemType();
            $em = $this->getEntityManager();
            $em->remove($schema);
            $em->flush();
            $this->setNoticeMessage('Schema eliminato con successo');
        } catch (Exception $exc) {
            $this->setErrorMessage("Si è verificato un errore durante l'operazione");
        }

        return $this->redirect($this->generateUrl('manteinance_schema_detail', array('id' => $sysType->getId())));
    }

    /**
     * @Route("/add", name="manteinance_schema_add")
     * @Route("/update/{id}", name="manteinance_schema_edit")
     * @Template()
     */
    public function addOrUpdateAction($id = null)
    {
        $schema = null;
        /* @var $schema MaintenanceSchema */

        if (isset($id)) {
            if (!($schema = $this->getEntityRepository()->findOneById($id))) {
                throw new \InvalidArgumentException("Invalid argument");
            }
        } else {
            $schema = new MaintenanceSchema();
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new MaintenanceSchemaForm(), $schema, array('validation_groups' => array('schema')));

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
        $dir = $dir ? strtolower($dir) : "up";
        if (!in_array($dir, array('up', 'down'))) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $sysType = $schema->getSystemType();
        $count = $sysType->getSchemas()->count() - 1;
        $index = $sysType->getSchemas()->indexOf($schema);

        if (($index == 0 && $dir == "up") || ($index == $count && $dir == "down")) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        if ($dir == "up") {
            $prevSchema = $sysType->getSchemas()->get($index - 1);
            $prevSchema->setListOrder($index);
            $schema->setListOrder($index - 1);
        } else {
            $nextSchema = $sysType->getSchemas()->get($index + 1);
            $nextSchema->setListOrder($index);
            $schema->setListOrder($index + 1);
        }
        $this->getEntityManager()->flush();

        return $this->redirect($this->generateUrl('manteinance_schema_detail', array('id' => $sysType->getId())));
    }

}

<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\Template as MyTemplate,
    Boilr\BoilrBundle\Entity\TemplateSection,
    Boilr\BoilrBundle\Form\TemplateForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Symfony\Component\HttpFoundation\Response,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;

class TemplateController extends BaseController
{

    public function __construct()
    {
        $this->entityName = 'BoilrBundle:Template';
    }

    /**
     * @Route("/list", name="template_list")
     * @Template()
     */
    public function listAction()
    {
        $templates = $this->getEntityRepository()->findBy(array(), array('name' => 'ASC'));

        return array('templates' => $templates);
    }

    /**
     * @Route("/{id}/sections", name="template_section_list")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function sectionListAction()
    {
        $template = $this->paramConverter('id');
        $sections = $template->getSections();

        return array('sections' => $sections, 'template' => $template, 'count' => count($sections));
    }

    /**
     * @Route("/delete/{id}", name="template_delete")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function deleteAction()
    {
        try {
            $template = $this->paramConverter('id');
            $dem = $this->getEntityManager();
            $dem->remove($template);
            $dem->flush();
            $this->setNoticeMessage("Modello eliminato con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage("Si è verificato un errore durante l'eliminazione.");
        }

        return $this->redirect($this->generateUrl('template_list'));
    }

    /**
     * @Route("/add", name="template_add")
     * @Route("{tid}/update", name="template_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function addOrUpdateAction($tid = null)
    {
        $opType = null;
        $template = null;

        if ($tid === null) {
            $opType = "add";
            $template = new MyTemplate();
        } else {
            $opType = "update";
            $template = $this->getEntityRepository()->findOneById($tid);
        }

        // Create the form, fill with data and select proper validation group
        $form = $this->createForm(new TemplateForm(), $template);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($opType == 'add') {
                        $dem->persist($template);
                    }
                    $dem->flush();
                    $this->setNoticeMessage('Operazione completata con successo');

                    return $this->redirect($this->generateUrl('template_section_list', array('id' => $template->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
            }
        }

        return array('form' => $form->createView(), 'optype' => $opType);
    }

    /**
     * @Route("/{id}/preview", name="template_preview")
     * @Template()
     */
    public function previewAction()
    {
        $template = $this->paramConverter('id');
        $html = $this->renderView('BoilrBundle:Template:pdf.html.twig', array('template' => $template));

        return new Response(
                        $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                        200, array(
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="file.pdf"'
                        )
        );
    }

}

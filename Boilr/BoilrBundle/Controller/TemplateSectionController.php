<?php

namespace Boilr\BoilrBundle\Controller;

use Boilr\BoilrBundle\Entity\TemplateSection,
    Boilr\BoilrBundle\Form\TemplateSectionForm,
    Boilr\BoilrBundle\Form\TemplateSectionOpForm;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Description of TemplateSectionController
 *
 * @author unixo
 */
class TemplateSectionController extends BaseController
{
    function __construct()
    {
        $this->entityName = 'BoilrBundle:TemplateSection';
    }

    /**
     * @Route("/{id}/delete", name="template_section_delete")
     * @ParamConverter("section", class="BoilrBundle:TemplateSection")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function deleteAction(TemplateSection $section)
    {
        try {
            $dem = $this->getEntityManager();
            $dem->remove($section);
            $dem->flush();
            $this->setNoticeMessage("Operazione conclusa con successo");
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->getLastRoute();
    }

    /**
     * @Route("/{tid}/add", name="template_section_add")
     * @Route("/{sid}/update", name="template_section_edit")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function addOrUpdateAction($tid = null, $sid = null)
    {
        $template = null;
        $section  = null;
        $opType   = null;

        // Guess if I'm adding a new section or updating an existing one
        if ($tid != null) {
            $template = $this->getDoctrine()->getRepository('BoilrBundle:Template')->findOneById($tid);
            if ($template) {
                $opType  = "add";
                $section = new TemplateSection();
                $section->setTemplate($template);
                $section->setListOrder($template->getSections()->count());
            }
        } else {
            $section = $this->getEntityRepository()->findOneById($sid);
            if ($section) {
                $template = $section->getTemplate();
                $opType   = "update";
            }
        }

        if ($template === null && $section === null) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $form = $this->createForm(new TemplateSectionForm(), $section);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $dem = $this->getEntityManager();
                    if ($opType == 'add') {
                        $dem->persist($section);
                    }
                    $dem->flush();
                    $this->setNoticeMessage("Operazione conclusa con successo");

                    return $this->redirect($this->generateUrl('template_section_list',
                            array('id' => $template->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'template' => $template, 'opType' => $opType);
    }

    /**
     * @Route("/{id}/move/{dir}", name="section_move")
     * @ParamConverter("section", class="BoilrBundle:TemplateSection")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function moveAction(TemplateSection $section, $dir = "down")
    {
        $_dir = strtolower($dir);
        if (! in_array($_dir, array('up', 'down'))) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        $template = $section->getTemplate();
        $count    = $template->getSections()->count()-1;
        $index    = $template->getSections()->indexOf($section);

        if (($index == 0 && $dir == "up") || ($index == $count && $dir == "down")) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        if ($dir == "up") {
            $prevSection = $template->getSections()->get($index-1);
            $prevSection->setListOrder($index);
            $section->setListOrder($index-1);
        } else {
            $nextSection = $template->getSections()->get($index+1);
            $nextSection->setListOrder($index);
            $section->setListOrder($index+1);
        }
        $this->getEntityManager()->flush();

        return $this->redirect($this->generateUrl('template_section_list', array('id' => $template->getId())));
    }

    /**
     * @Route("/{id}/bind-operations", name="template_section_bind")
     * @ParamConverter("section", class="BoilrBundle:TemplateSection")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     * @Template()
     */
    public function bindOperationsAction(TemplateSection $section)
    {
        $form = $this->createForm(new TemplateSectionOpForm(), $section);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                try {
                    $this->getEntityManager()->flush();
                    $this->setNoticeMessage('Operazione conclusa con successo');
                    return $this->redirect($this->generateUrl('template_section_list',
                              array('id' => $section->getTemplate()->getId())));
                } catch (Exception $exc) {
                    $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
                }
            }
        }

        return array('form' => $form->createView(), 'section' => $section);
    }

    /**
     * @Route("/{id}/unbind-operation/{pid}", name="template_section_unbind")
     * @ParamConverter("section", class="BoilrBundle:TemplateSection")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER")
     */
    public function unbindOperationAction(TemplateSection $section, $pid)
    {
        $operation = $this->getDoctrine()->getRepository('BoilrBundle:Operation')->findOneById($pid);
        if (! $operation) {
            throw new \InvalidArgumentException("Invalid argument");
        }

        try {
            $section->getOperations()->removeElement($operation);
            $this->getEntityManager()->flush();
            $this->setNoticeMessage('Operazione eliminata dalla sezione');
        } catch (Exception $exc) {
            $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
        }

        return $this->redirect($this->generateUrl('template_section_list',
                              array('id' => $section->getTemplate()->getId())));
    }
}

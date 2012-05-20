<?php

namespace Boilr\BoilrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\Security\Core\SecurityContext,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter,
    JMS\SecurityExtraBundle\Annotation\Secure;
use Boilr\BoilrBundle\Form\PolicyResultForm;

class PolicyController extends BaseController
{

    const KEY_SORTED = "sorted";
    const KEY_UNSORTED = "unsorted";

    static protected $policies = array(
        "\Boilr\BoilrBundle\Policy\EqualBalancedPolicy",
        "\Boilr\BoilrBundle\Policy\FillupPolicy",
    );

    /**
     * Returns all interventions without an installer, sorted by date
     *
     * @return array
     */
    protected function interventionsWithoutInstaller()
    {
        $allInterventions = $this->getDoctrine()->getRepository('BoilrBundle:MaintenanceIntervention')
                ->findBy(array('installer' => null), array('scheduledDate' => 'ASC'));
        $results = array();

        foreach ($allInterventions as $interv) {
            $key = $interv->getScheduledDate()->format('Y-m-d');
            $results[$key][] = $interv;
        }
        ksort($results, SORT_STRING);

        $interventions = array(
            self::KEY_SORTED => $results,
            self::KEY_UNSORTED => $allInterventions,
        );

        return $interventions;
    }

    /**
     * Returns an assignment policy reference if found by name
     *
     * @param string $policyName
     * @return \ReflectionClass|null
     */
    protected function policyClassByName($policyName)
    {
        foreach (self::$policies as $aPolicyName) {
            $reflector = new \ReflectionClass($aPolicyName);
            $method = $reflector->getMethod("getName");
            $name = $method->invoke(null);

            if ($name === $policyName) {
                return $reflector;
            }
        }

        return null;
    }

    protected function resultsForPolicy($policyClassName)
    {
        if (!is_string($policyClassName)) {
            throw new \InvalidArgumentException('invalid argument');
        }

        $interventions = $this->interventionsWithoutInstaller();
        $installers = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->findAll();

        $policyClass = $this->policyClassByName($policyClassName);
        $policy = $policyClass->newInstance($this->getEntityManager(), $this->get('logger'));
        $policy->setInstallers($installers);
        $policy->setInterventions($interventions[self::KEY_SORTED]);

        $policy->elaborate();
        $results = $policy->getResult();

        return $results;
    }

    /**
     * Edit a assignment result
     *
     * @Route("/{name}/edit-result", name="policy_edit_assignments")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Template()
     */
    public function editAssignmentAction($name)
    {
        $result = $this->resultsForPolicy($name);
        $form = $this->createForm(new PolicyResultForm(), $result);

        if ($this->isPOSTRequest()) {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Export given intervention in XML
     *
     * @Route("/assignment-wizard", name="policy_assignment_wizard")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Method("get")
     * @Template()
     */
    public function assignmentWizardAction()
    {
        $interventions = $this->interventionsWithoutInstaller();
        $installers = array();

        if (count($interventions) > 0) {
            $_installers = new \Doctrine\Common\Collections\ArrayCollection();

            foreach ($interventions[self::KEY_UNSORTED] as $interv) {
                $details = $interv->getDetails();
                $systemType = $details[0]->getSystem()->getSystemType();
                foreach ($systemType->getInstallers() as $inst) {
                    if (!$_installers->contains($inst)) {
                        $_installers->add($inst);
                    }
                }
            }

            foreach ($_installers as $inst) {
                $entry['id'] = $inst->getId();
                $entry['name'] = $inst->getFullName();
                $entry['load'] = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->getLoadForInstaller($inst);
                $entry['abilities'] = $inst->getAbilitiesDescr();

                $installers[] = $entry;
            }
        }

        return array('interventions' => $interventions[self::KEY_UNSORTED], 'installers' => $installers);
    }

    /**
     * Apply equal balanced policy
     *
     * @Route("/{name}/preview", name="intervention_preview_policy")
     * @Secure(roles="ROLE_ADMIN, ROLE_SUPERUSER, ROLE_OPERATOR")
     * @Method("get")
     * @Template()
     */
    public function previewPolicyResultsAction($name)
    {
        $result = $this->resultsForPolicy($name);
        /* @var $result \Boilr\BoilrBundle\Form\Model\PolicyModel */

        if ($this->getRequest()->get('doit', false)) {
            try {
                foreach ($result->getAssociations() as $entry) {
                    /* @var $entry \Boilr\BoilrBundle\Form\Model\InstallerForIntervention */
                    $entry->getIntervention()->setInstaller($entry->getInstaller());
                }
                $this->getEntityManager()->flush();
                $this->setNoticeMessage('Interventi assegnati con successo');

                return $this->redirect($this->generateUrl('intervention_assignment_wizard'));
            } catch (Exception $exc) {
                $this->setErrorMessage('Si è verificato un errore durante il salvataggio');
            }
        }

        return array('result' => $result);
    }

}

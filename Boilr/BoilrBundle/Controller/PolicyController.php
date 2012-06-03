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

    protected static $policies = array(
        "\Boilr\BoilrBundle\Policy\EqualBalancedPolicy",
        "\Boilr\BoilrBundle\Policy\FillupPolicy",
        "\Boilr\BoilrBundle\Policy\WaypointPolicy",
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
     * @param  string                $policyName
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

    /**
     * Returns an instance of a policy class identified by parameter.
     *
     * @param  string                                              $policyClassName
     * @return \Boilr\BoilrBundle\Policy\AssignmentPolicyInterface
     * @throws \InvalidArgumentException
     */
    protected function instantiatePolicyClass($policyClassName)
    {
        $policyClass = $this->policyClassByName($policyClassName);
        if (null === $policyClass) {
            throw new \InvalidArgumentException('invalid policy class name');
        }

        // parameters to be passed to assignment policy
        $logger = $this->get('logger');
        $directionHelper = $this->get('google_direction');
        $dem = $this->getEntityManager();
        $user = $this->getCurrentUser();

        $policy = $policyClass->newInstance($dem, $directionHelper, $logger, $user);

        return $policy;
    }

    /**
     *
     * @param  string                    $policyClassName
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function resultsForPolicy($policyClassName)
    {
        if (!is_string($policyClassName)) {
            throw new \InvalidArgumentException('invalid argument');
        }

        $interventions = $this->interventionsWithoutInstaller();
        $installers = $this->getDoctrine()->getRepository('BoilrBundle:Installer')->findAll();

        $policy = $this->instantiatePolicyClass($policyClassName);
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
                $policy = $this->instantiatePolicyClass($name);
                $result = $policy->apply($result);

                if ($result === true) {
                    $this->setNoticeMessage("Associazione effettuata con successo");

                    return $this->redirect($this->generateUrl('policy_assignment_wizard'));
                } else {
                    $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
                }
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
            $policy = $this->instantiatePolicyClass($name);
            $result = $policy->apply($result);

            if ($result === true) {
                $this->setNoticeMessage("Associazione effettuata con successo");

                return $this->redirect($this->generateUrl('policy_assignment_wizard'));
            } else {
                $this->setErrorMessage("Si è verificato un errore durante il salvataggio");
            }
        }

        return array('result' => $result);
    }

}

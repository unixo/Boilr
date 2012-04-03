<?php

namespace Boilr\BoilrBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;

class LinkInstallerFlow extends FormFlow
{
    protected $maxSteps = 2;

    protected function loadStepDescriptions()
    {
        return array('Selezione', 'Riepilogo');
    }
}
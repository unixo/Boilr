<?php

namespace Boilr\BoilrBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;

class NewPersonFlow extends FormFlow
{
    protected $maxSteps = 3;

    protected function loadStepDescriptions()
    {
        return array('Anagrafica', 'Indirizzi', 'Riepilogo');
    }
}
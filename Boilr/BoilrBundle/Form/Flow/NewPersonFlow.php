<?php

namespace Boilr\BoilrBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;

class NewPersonFlow extends FormFlow
{
    protected $maxSteps = 4;

    protected function loadStepDescriptions()
    {
        return array('Anagrafica', 'Indirizzi', 'Impianti', 'Riepilogo');
    }
}
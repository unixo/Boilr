<?php

namespace Boilr\BoilrBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Description of EqualsField
 *
 * @author unixo
 */
class EqualsField extends Constraint
{
    public $message = 'This value is not equal to {{ field }}';

    public $field;

    public $negate = false;


    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('field');
    }
}

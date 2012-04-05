<?php

namespace Boilr\BoilrBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class WorkingDay extends Constraint
{
    public $message = 'This value is not a working day';
}

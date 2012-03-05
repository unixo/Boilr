<?php

namespace Boilr\BoilrBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @api
 */
class CustomDate extends Constraint
{
    public $message = 'ABBO This value is not a valid date';

    public $pattern = '/^(\d{4})-(\d{2})-(\d{2})$/';
}

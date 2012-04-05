<?php

namespace Boilr\BoilrBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator,
    Symfony\Component\Validator\Exception\UnexpectedTypeException;

use Boilr\BoilrBundle\Extension\MyDateTime;

/**
 * @api
 */
class WorkingDayValidator extends ConstraintValidator
{

    public function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (! ($value instanceof \DateTime)) {
            return false;
        }

        return MyDateTime::isWorkingDay($value);
    }
}

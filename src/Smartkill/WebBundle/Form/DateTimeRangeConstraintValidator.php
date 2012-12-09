<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeRangeConstraintValidator extends ConstraintValidator {
	
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (null !== $constraint->max && $value > $constraint->max) {
            $this->context->addViolation($constraint->maxMessage, array(
                '{{ limit }}' => $constraint->max->format($constraint::FORMAT),
            ));

            return;
        }

        if (null !== $constraint->min && $value < $constraint->min) {
            $this->context->addViolation($constraint->minMessage, array(
                '{{ limit }}' => $constraint->min->format($constraint::FORMAT),
            ));
        }
    }
}

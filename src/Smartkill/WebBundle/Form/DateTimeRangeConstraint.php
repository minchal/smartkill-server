<?php

namespace Smartkill\WebBundle\Form;

use Symfony\Component\Validator\Constraint;

class DateTimeRangeConstraint extends Constraint {
	
	const FORMAT = 'Y-m-d H:i:s';
	
    public $minMessage = 'This date should be {{ limit }} or greater.';
    public $maxMessage = 'This date should be {{ limit }} or smaller.';
    public $min;
    public $max;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->min && null === $this->max) {
            throw new MissingOptionsException('Either option "min" or "max" must be given for constraint ' . __CLASS__, array('min', 'max'));
        }
    }
}

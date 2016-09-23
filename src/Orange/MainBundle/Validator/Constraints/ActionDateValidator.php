<?php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ActionDateValidator extends ConstraintValidator
{

	public function validate($action, Constraint $constraint)
	{
		if($action->getDateInitial() && $action->getDateDebut())
		{
			$dateInitial = $action->getDateInitial()->format('Ymd');
			$dateDebut 	 = $action->getDateDebut()->format('Ymd');
			if($dateInitial < $dateDebut)
			{
				$this->context->addViolationAt('dateInitial', $constraint->messageDateInitial, array(), null);
			}
		}
	}
}
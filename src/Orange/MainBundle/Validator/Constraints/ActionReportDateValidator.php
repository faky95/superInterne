<?php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ActionReportDateValidator extends ConstraintValidator
{

	public function validate($report, Constraint $constraint)
	{
		if($report->getAction()->getDateInitial() && $report->getDate())
		{
			$dateReport      = $report->getDate()->format('Ymd');
			$delai           = $report->getAction()->getDateInitial()->format('Ymd');
			if($delai > $dateReport)
			{
				$this->context->addViolationAt('date', $constraint->messageDate, array(), null);
			}
		}
	}
}
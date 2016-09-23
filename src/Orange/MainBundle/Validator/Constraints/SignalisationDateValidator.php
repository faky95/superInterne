<?php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SignalisationDateValidator extends ConstraintValidator
{

	public function validate($signalisation, Constraint $constraint)
	{
		
		$today =  new \DateTime();
		$dateConstatSignalisation = $signalisation->getDateConstat();
		
		if($dateConstatSignalisation)
		{
			$dateConstatSignalisation = $dateConstatSignalisation->format('Ymd');
			$today 					  = $today->format('Ymd');
			if($today < $dateConstatSignalisation)
			{
				$this->context->addViolationAt('dateConstat', $constraint->messageDateConstat, array(), null);
			}
		}
	}
}
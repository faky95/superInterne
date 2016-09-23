<?php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Orange\MainBundle\Utils\ActionUtils;

class CycliquePeriodiciteValidator extends ConstraintValidator
{

	public function validate($actionCyclique, Constraint $constraint)
	{
		if($actionCyclique->getPeriodicite() && $actionCyclique->getAction()->getDateDebut() && $actionCyclique->getAction()->getDateInitial() )
		{
			
			$periodicite 	= $actionCyclique->getPeriodicite();
			switch ($periodicite)
			{
				case 'HEBDOMADAIRE':
					$intervalle = 7;
					break;
				case 'MENSUEL':
					$intervalle = 31;
					break;
				case 'BIMESTRE':
					$intervalle = 62;
					break;
				case 'TRIMESTRE':
					$intervalle = 93;
					break;
				case 'SEMESTRE':
					$intervalle = 246;
					break;
				case 'ANNUEL':
					$intervalle = 365;
					break;
			}
			$dateDebut 		= $actionCyclique->getAction()->getDateDebut()->format("d-m-Y");
			$dateInitial 	= $actionCyclique->getAction()->getDateInitial()->format("d-m-Y");
			
			$dateDiff = ActionUtils::dateDiff($dateInitial, $dateDebut);
			if($intervalle > $dateDiff['day'])
			{
				$this->context->addViolationAt('periodicite', $constraint->messageCycliquePeriodicite, array(), null);
			}
		}
	}
}
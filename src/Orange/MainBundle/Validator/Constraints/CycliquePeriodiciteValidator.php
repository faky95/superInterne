<?php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Orange\MainBundle\Utils\ActionUtils;

class CycliquePeriodiciteValidator extends ConstraintValidator {

	public function validate($actionCyclique, Constraint $constraint) {
		if($actionCyclique->getPas() && $actionCyclique->getAction()->getDateDebut() && $actionCyclique->getAction()->getDateInitial()) {
			$periodicite 	= $actionCyclique->getPas();
			switch(strtoupper($periodicite)) {
				case 'HEBDOMADAIRE':
					$intervalle = 7;
					break;
				case 'MENSUEL':
					$intervalle = 31;
					break;
				case 'BIMESTRIEL':
					$intervalle = 62;
					break;
				case 'TRIMESTRIEL':
					$intervalle = 93;
					break;
				case 'SEMESTRIEL':
					$intervalle = 246;
					break;
				case 'ANNUEL':
					$intervalle = 365;
					break;
			}
			$dateDebut 		= $actionCyclique->getAction()->getDateDebut()->format("d-m-Y");
			$dateInitial 	= $actionCyclique->getAction()->getDateInitial()->format("d-m-Y");
			
			$dateDiff = ActionUtils::dateDiff($dateInitial, $dateDebut);
			if($intervalle > $dateDiff['day']) {
				$this->context->addViolationAt('periodicite', $constraint->messageCycliquePeriodicite, array(), null);
			}
		}
	}
}
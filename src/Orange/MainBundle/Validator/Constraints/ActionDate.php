<?php
// src/Sdz/BlogBundle/Validator/AntiFlood.php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ActionDate extends Constraint
{
  public $messageDateDebut 	 = 'La date de début de l\'action ne peut être inférieur à la date courante !';
  public $messageDateInitial = 'La date du délai initial ne peut être inférieur à la date de début !';

  public function validatedBy()
  {
    return 'action_date'; // Ici, on fait appel à l'alias du service
  }
  
  public function getTargets()
  {
	return self::CLASS_CONSTRAINT;
  }
}
<?php
// src/Sdz/BlogBundle/Validator/AntiFlood.php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SignalisationDate extends Constraint
{
  public $messageDateConstat = 'La date du constat ne peut être supérieure à la date d\'aujourdhui!';

  public function validatedBy()
  {
    return 'signalisation_date'; // Ici, on fait appel à l'alias du service
  }
  
  public function getTargets()
  {
	return self::CLASS_CONSTRAINT;
  }
}
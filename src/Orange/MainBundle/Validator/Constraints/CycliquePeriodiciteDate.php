<?php
// src/Sdz/BlogBundle/Validator/AntiFlood.php

namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CycliquePeriodiciteDate extends Constraint
{
  public $messageCycliquePeriodicite = 'La périodicité ne peut pas être supérieure au délai choisi. ';

  public function validatedBy()
  {
    return 'cyclique_periodicite'; // Ici, on fait appel à l'alias du service
  }
  
  public function getTargets()
  {
	return self::CLASS_CONSTRAINT;
  }
}
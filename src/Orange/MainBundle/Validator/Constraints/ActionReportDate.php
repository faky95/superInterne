<?php
namespace Orange\MainBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ActionReportDate extends Constraint
{
  public $messageDate 	 = 'La date de report de l\'action doit être supérieure au délai initial !';

  public function validatedBy()
  {
    return 'report_date'; // Ici, on fait appel à l'alias du service
  }
  
  public function getTargets()
  {
	return self::CLASS_CONSTRAINT;
  }
}
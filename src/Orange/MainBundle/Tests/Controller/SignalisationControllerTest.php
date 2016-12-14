<?php
namespace Orange\MainBundle\Tests\Controller;

use Orange\QuickMakingBundle\Tests\Controller\BaseControllerTest;

class SignalisationControllerTest extends BaseControllerTest
{
	/**
	 * @array
	 */
	protected $connexion = array('username' => 'sylla060210', 'password' => 'Compil_87');

	/**
	 * @return array 
	 */
	public function urlProvider() {
		return array(
			 array("/-1/edition_signalisation"),
			 array("/-1/supprimer_signalisation"),
			 array("/ss_instance"),
			 array("/validation_signalisation/valide/-1"),
			 array("/actions_correctives/-1"),
			 array("/reload_actions/-1"),
			 array("/chargement_signalisation"),
			 array("/typesignalisation_by_instance"),
			 array("/domaine_signalisation_by_instance"),
		);
	}
	
	
}

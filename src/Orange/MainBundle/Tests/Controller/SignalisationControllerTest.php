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
			 array("/les_signalisations"),
			 array("/7/edition_signalisation"),
			 array("/ss_instance"),
			 array("/validation_signalisation/SIGN_VALIDE/7"),
			 array("/actions_correctives/7"),
			 array("/reload_actions/7"),
			 array("/chargement_signalisation"),
			 array("/typesignalisation_by_instance"),
			 array("/domaine_signalisation_by_instance"),
			 array("/7/supprimer_signalisation")
		);
	}
	
	
}

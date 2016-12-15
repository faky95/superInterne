<?php

namespace Orange\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ActionStatutControllerTest extends WebTestCase
{

	/**
	 * @array
	 */
	protected $connexion = array('username' => 'sylla060210', 'password' => 'orange');
	
	/**
	 * @return array
	 */
	public function urlProvider() {
		return array(
				array("/actionstatut/traitement/943"),
				array("/actionstatut/liste_invalidation/943"),
				array("/actionstatut/choix_proposition/66"),
				array("/actionstatut/solder_action/943"),
				array("/actionstatut/demande_action/943"),
				array("/actionstatut/validation_action/943"),
				array("/actionstatut/"),
				array("/actionstatut/creer_action_statut/943"),
				array("/actionstatut/action_statut_nouveau/943"),
				array("/actionstatut/943"),
				array("/actionstatut/943/edit"),
				array("/actionstatut/historique/943"),
				array("/actionstatut/demande_abandon_nouveau/943"),
				array("/actionstatut/creer_abandon/943"),
				array("/actionstatut/cloturer_action/943")
		);
	}
}

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
				array("/actionstatut/traitement/-1"),
				array("/actionstatut/liste_invalidation/-1"),
				array("/actionstatut/choix_proposition/-2"),
				array("/actionstatut/solder_action/-1"),
				array("/actionstatut/demande_action/-1"),
				array("/actionstatut/validation_action/-1"),
				array("/actionstatut/"),
				array("/actionstatut/creer_action_statut/-1"),
				array("/actionstatut/action_statut_nouveau/-1"),
				array("/actionstatut/-1"),
				array("/actionstatut/-1/edit"),
				array("/actionstatut/historique/-1"),
				array("/actionstatut/demande_abandon_nouveau/-1"),
				array("/actionstatut/creer_abandon/-1"),
				array("/actionstatut/cloturer_action/-1")
		);
	}
}

<?php

namespace Orange\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignalisationStatutControllerTest extends WebTestCase
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
				array("/signalisationstatut/"),
				array("/signalisationstatut/signalisation_statut_nouveau/SIGN_NOUVELLE/-1"),
				array("/signalisationstatut/-1/edit")
		);
	}

}

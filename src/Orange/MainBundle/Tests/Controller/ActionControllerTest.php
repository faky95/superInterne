<?php
namespace Orange\MainBundle\Tests\Controller;

use Orange\QuickMakingBundle\Tests\Controller\BaseControllerTest;

class ActionControllerTest extends BaseControllerTest
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
			 array("/les_mails"),
			 array("ACTION_NON_ECHUE/les_actions_validees"),
			 array("/les_actions"),
			 array("ACTION_NON_ECHUE/1/les_actions_by_statut"),
			 array("ACTION_NON_ECHUE/les_actions_by_statut"),
			 array("507/les_actions_by_instance"),
			 array("158/les_actions_by_structure"),
			 array("1/les_actions_by_espace"),
			 array("/mes_actions"),
			 array("/actions_collaborateurs"),
			 array("/liste_actions_collaborateurs"),
			 array("/liste_des_actions"),
			 array("ACTION_NON_ECHUE/liste_des_actions_validees"),
			 array("ACTION_NON_ECHUE/liste_des_actions_by_statut?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("507/liste_des_actions_by_instance?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("158/liste_des_actions_by_structure?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/liste_de_mes_actions?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/1/liste_by_espace?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("ACTION_NON_ECHUE/1/liste_des_actions_by_statut?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/filtrer_actions"),
// 			 array("/export_action"),
			 array("/nouvelle_action"),
			 array("/507/nouvelle_action_to_instance"),
			 array("/1/nouvelle_action_to_espace"),
			 array("/943/changer_statut"),
			 array("/943/reassignation_action"),
			 array("/signalisation/7/nouvelle_action"),
			 array("/details_action/943"),
			 array("/edition_action/943"),
			 array("/chargement_action/1"),
			 array("/select_utilisateurs"),
			 array("/porteur_by_espace"),
			 array("/porteur_by_instance"),
			 array("/user_by_instance"),
			 array("/type_by_instance"),
			 array("/domaine_by_instance")
		);
	}
	
}

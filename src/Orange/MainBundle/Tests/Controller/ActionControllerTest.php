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
			 array("{code}/les_actions_validees"),
			 array("/les_actions"),
			 array("{code_statut}/{espace_id}/les_actions_by_statut"),
			 array("{code_statut}/les_actions_by_statut"),
			 array("{instance_id}/les_actions_by_instance"),
			 array("{structure_id}/les_actions_by_structure"),
			 array("{espace_id}/les_actions_by_espace"),
			 array("/mes_actions"),
			 array("/actions_collaborateurs"),
			 array("/liste_actions_collaborateurs"),
			 array("/liste_des_actions"),
			 array("{code}/liste_des_actions_validees"),
			 array("{code_statut}/liste_des_actions_by_statut?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("{instance_id}/liste_des_actions_by_instance?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("{structure_id}/liste_des_actions_by_structure?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/liste_de_mes_actions?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/{espace_id}/liste_by_espace?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("{code_statut}/{espace_id}/liste_des_actions_by_statut?iDisplayStart=0&iDisplayLength=10&sEcho=1"),
			 array("/filtrer_actions"),
// 			 array("/export_action"),
			 array("/nouvelle_action"),
			 array("/{instance_id}/nouvelle_action_to_instance"),
			 array("/{espace_id}/nouvelle_action_to_espace"),
			 array("/{entity_id}/changer_statut"),
			 array("/{action_id}/reassignation_action"),
			 array("/signalisation/{signalisation_id}/nouvelle_action"),
			 array("/details_action/{id}"),
			 array("/edition_action/{id}"),
			 array("/chargement_action/{isCorrective}"),
			 array("/select_utilisateurs"),
			 array("/porteur_by_espace"),
			 array("/porteur_by_instance"),
			 array("/user_by_instance"),
			 array("/type_by_instance"),
			 array("/domaine_by_instance")
		);
	}
	
}

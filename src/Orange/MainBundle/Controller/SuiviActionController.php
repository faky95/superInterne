<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Form\ActionType;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 *
 * Validation Action controller.
 * @Route("/suivi_action")
 */
class SuiviActionController extends Controller
{
	
	/**
	 * @Route("/action/{valide}/{action_id}", name="valider_action")
	 */
	public function validationAction(Request $request, $valide, $action_id){
		$em   = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		ActionUtils::changeStatutAction($em, $action, $valide, $this->getUser()); // Action valide est égale à la réalisation
		switch ($valide){
			case Statut::ACTION_VALIDER :
				ActionUtils::changeStatutAction($em, $action, Statut::ACTION_TRAITEMENT, $this->getUser());
				return $this->redirect($this->generateUrl('details_action', array('id' => $action_id)));
			break;
			case Statut::ACTION_INVALIDER :
				if($this->getUser()->hasRole(Utilisateur::ROLE_PORTEUR) && ($this->getUser()->getId() === $action->getPorteur()->getId())){
					return $this->porteurInvalidationAction($action);
				}
				elseif ($this->getUser()->hasRole(Utilisateur::ROLE_MANAGER))
				{
					return $this->managerAction($action);
				}			
			break;
		}
		return $this->redirect($this->generateUrl('details_action', array('id' => $action_id)));
	}
		
	public function porteurInvalidationAction($action){
		$em = $this->getDoctrine()->getManager();
		$niveauValidationParametre = $action->getInstance()->getNiveauValidation()->getNiveau(); 
		$niveauValidationStructure = $action->getPorteur()->getStructure()->getTypeStructure()->getNiveau();
		if(strnatcmp($niveauValidationParametre, $niveauValidationStructure) < 0 ){
			$etape = Statut::ACTION_DEMANDE_ABANDON;
		}else{
			$etape = Statut::VALIDATION_ACTION_EN_ESCALADE;
		}
		ActionUtils::changeStatutAction($em, $action, $etape, $this->getUser());
		return $this->redirect($this->generateUrl('details_action', array('id' => $action->getId())));	//ieonline.microsoft.com/#ieslice
	}
	
	/**
	 * @Route("/demande_porteur/{demande}/{action_id}", name="demande_porteur")
	 */
	public function avancementAction($action_id, $demande){
		$em = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		ActionUtils::changeStatutAction($em, $action, $demande, $this->getUser());
		return $this->redirect($this->generateUrl('details_action', array('id' => $action_id)));
	}
	
	/**
	 * @Route("/escalade_validation/{action_id}", name="escalade_validation_action")
	 */
	public function escaladeAction($action_id){
		$em = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		$managerStatutAction = $em->getRepository('OrangeMainBundle:ActionStatut')->getLastManagerValidation($action_id);
		if($managerStatutAction === NULL){
			$structureAction = $em->getRepository('OrangeMainBundle:Action')->find($action_id)->getPorteur()->getStructure();
			$manager = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureAction->getId(), 'manager' => true));
		}else{
			$niveauValidationParametre = $em->getRepository('OrangeMainBundle:Action')->find($action_id)->getInstance()->getNiveauValidation()->getNiveau();
			$niveauManagerStatutAction = $managerStatutAction->getUtilisateur()->getStructure()->getTypeStructure()->getNiveau();
			if(($niveauManagerStatutAction != $niveauValidationParametre) && ($niveauManagerStatutAction != 'NIVEAU4')){
				$structureManagerSuperieur = $managerStatutAction->getUtilisateur()->getStructure()->getParent();
				$manager = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureManagerSuperieur->getId(), 'manager' => true ));
			}
		}
		ActionUtils::changeStatutAction($em, $action, Statut::VALIDATION_MANAGER_ATTENTE, $manager);
		return $this->redirect($this->generateUrl('details_action', array('id' => $action_id)));
	}
	
	public function managerAction($action){
		$em = $this->getDoctrine()->getManager();
		$managerNiveau = $this->getUser()->getStructure()->getTypeStructure()->getNiveau();
		$niveauValidationParametre = $action->getInstance()->getNiveauValidation()->getNiveau();
		if(($managerNiveau != $niveauValidationParametre) && ($managerNiveau != 'NIVEAU4')){
			$etape = Statut::VALIDATION_ACTION_EN_ESCALADE;
		}
		else{
			$etape = Statut::ACTION_DEMANDE_ABANDON;
		}
		ActionUtils::changeStatutAction($em, $action, $etape, $this->getUser());
		return $this->redirect($this->generateUrl('details_action', array('id' => $action->getId())));
	}
			
	/**
	 * @Route("/suivi_traitement/{suivi}/{action_id}", name="suivi_traitement")
	 */
	public function suiviAction($action_id, $suivi){
		$em = $this->getDoctrine()->getManager();
		$action = $em->getRepository('OrangeMainBundle:Action')->find($action_id);
		ActionUtils::changeStatutAction($em, $action, $suivi, $this->getUser());
		
		if($suivi === Statut::ACTION_DEMANDE_ABANDON_REFUS || $suivi === Statut::ACTION_NON_SOLDEE){
			ActionUtils::changeStatutAction($em, $action, Statut::ACTION_TRAITEMENT, $this->getUser());
		}
		elseif ($suivi === Statut::ACTION_NON_EFFICACE){
			ActionUtils::changeStatutAction($em, $action, Statut::ACTION_ARCHIVE, $this->getUser());
		}
		
		return $this->redirect($this->generateUrl('details_action', array('id' => $action_id)));
	}
	
}
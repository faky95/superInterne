<?php

namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\Tache;
use Orange\MainBundle\Entity\TacheStatut;
use Orange\MainBundle\Entity\Statut;

class WorkflowUtils {

	public static function actionWorkflow($entityManager, $actionStatut, $currentUser)
	{
		$now = new \DateTime ();
		$now = $now->format ( 'd-m-Y' ) . " à " . $now->format ( 'H:i:s' );
		$action = $actionStatut->getAction();
		$allActionStatut = $entityManager->getRepository("OrangeMainBundle:ActionStatut")->findByAction($action->getId());
		$previousStatut = $allActionStatut [count ( $allActionStatut ) - 2];
		$previousStatutCode = $previousStatut->getStatut ()->getCode ();
		$animateur = $allActionStatut[0]->getUtilisateur();
		$porteur = $action->getPorteur();
		$actionStatutCode = $actionStatut->getStatut()->getCode();
		
		switch ($previousStatutCode){
			
			case Statut::ACTION_NOUVELLE:
				if($actionStatutCode === Statut::VALIDER)
				{
					$NextStep = Statut::ACTION_NON_ECHUE;
					$subject = 'Prise en charge de l\'action';
					$commentaire = 'Le ' . $now . ' . La prise en charge de l\'action a été confirmé par ' . $porteur . '
									L\'action est en cours de traitement. ';
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$NextStep = Statut::EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE;
					$subject = 'Contre proposition sur l\'action';
					$commentaire = 'Le ' . $now . ' . ' . $porteur . ', porteur désigné dans le traitement de l\'action :' . $action->getLibelle() . '
									a porté des objections quant à la prise en charge de cette action. Objection du porteur : ' . $actionStatut->getCommentaire () . '.
									Il est demandé à ' . $animateur . ', animateur de l\'instance ' . $action->getInstance () . '
									de donner une suite à cette demande !';
				}
			break;
			case Statut::EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE:
				$subject = 'Contre proposition sur l\'action: Retour de l\'animateur ';
				if($actionStatutCode === Statut::VALIDER)
				{
					$NextStep = Statut::ACTION_NON_ECHUE;
					$commentaire = 'Le ' . $now . ', l\'animateur  ' . $animateur . ' a effectué un retour favorable suite à la proposition de '.$porteur.'
									sur la prise en charge de l\'action. Ses objections seront prises en compte dans la modification de l\'action. Il est
									demandé à '.$animateur.' de modifier l\'action en prenant en compte ces dernières remarques.';
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$NextStep = Statut::EVENEMENT_VALIDATION_MANAGER_ATTENTE;
					$commentaire = 'Le ' . $now . ', l\'animateur  ' . $animateur . ' a effectué un retour non favorable suite à la proposition de '.$porteur.'
									sur la prise en charge de l\'action. Il est demandé à '.$manager.', responsable hierarchique de '.$porteur.'. 
									de faire arbritage entre les différentes propositions.';
					
				}
			break;
			case Statut::EVENEMENT_VALIDATION_MANAGER_ATTENTE:
				if($actionStatutCode === Statut::VALIDER)
				{
					$subject = 'Contre proposition sur l\'action: Choix du manager';
					$NextStep = Statut::ACTION_NON_ECHUE;
					$commentaire = 'Le ' . $now . ',  ' . $currentUser . ' manager impliqué dans l\'arbritage, suite à la contre proposition du porteur dans la
									prise en charge de l\'action '.$action->getLibelle().' a effectué son arbritage en choisissant la proposition suivante :'
									.$actionStatut->getCommentaire().'. Cette proposition fera foi. Il est demandé à l\'animateur '.$animateur.' de modifier l\'action
									en prenant en compte ce choix. Le porteur de l\'action '.$porteur. ' est invité à reprendre le traitement de cette action' ;
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$subject = 'Contre proposition sur l\'action: Proposition du manager';
					$NextStep = Statut::ACTION_NON_ECHUE;
					if(self::isLastManagerValidation($entityManager, $actionStatut))
					{
						$commentaire = 'Le ' . $now . ',  '.$currentUser.', manager impliqué dans le traitement de l\'action intitulé '.$action->getLibelle().'
										a rejeté toutes les objections liées à la prise en charge de cette action et a donc soumis sa propre proposition qui est la suivante :'.$actionStatut->getCommentaire().' . Sa proposition fera foi.
										il est demandé à l\'animateur d\'instance, '.$animateur.' de prendre en charge ces modifications.';
					}
					else
					{
						$managerPlus = self::getManagerSup($entityManager, $actionStatut);
						$commentaire = 'Le ' .$now. ',  '.$actionStatut->getUtilisateur().', manager impliqué dans le traitement de l\'action intitulé '.$action->getLibelle().'
										a rejeté toutes les objections liées à la prise en charge de cette action et a donc soumis sa propre proposition qui est la suivante :
										'.$actionStatut->getCommentaire().' . Il est demandé à '.$managerPlus.' supérieur hierarchique de '.$actionStatut->getUtilisateur().' de faire
										arbritage entre toutes les propositions précédentes';
					} 
				}
			break;
			case Statut::EVENEMENT_DEMANDE_DE_REPORT:
				if($actionStatutCode === Statut::VALIDER)
				{
					self::updateDelai($entityManager, $action);
					$NextStep = Statut::EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE;
					$subject = 'Report d\'échéance accepté';
					$commentaire = 'Le ' . $now . '. La demande de report d\'échéance a été accepté par ' . $animateur . ', animateur de l\'instance '
								   . $action->getInstance(). ' ! '.$porteur.' est invité à reprendre le traitement de l\'action .';
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$NextStep = Statut::EVENEMENT_DEMANDE_DE_REPORT_REFUS;
					$subject = 'Rejet de la demande de report';
					$commentaire = 'Le ' . $now . '. La demande de report d\'échéance a été rejetée par ' . $animateur . ', animateur de l\'instance '
								   . $action->getInstance().'. '.$porteur.' est invité à reprendre le traitement de cette action .';
				}
			break;
			case Statut::EVENEMENT_DEMANDE_ABANDON:
				if($actionStatutCode === Statut::VALIDER)
				{
					$NextStep = Statut::ACTION_ABANDONEE;
					$subject = 'Report d\'abandon accepté';
					$commentaire = 'Le ' . $now . '. La demande d\'abandon de l\'action a été acceptée par ' . $animateur . ', animateur de l\'instance '
									. $action->getInstance().'. L\'action a été abandonné.';
				}
				elseif($actionStatutCode === Statut::INVALIDER)
				{
					$NextStep = Statut::ACTION_NON_ECHUE;
					$subject = 'Rejet de la demande d\'abandon';
					$commentaire = 'Le ' . $now . '. La demande d\'abandon de l\'action a été rejetée par ' . $animateur . ', animateur de l\'instance '
									. $action->getInstance().'. '.$porteur.' est invité à reprendre le traitement de cette action .';
				}
			break;
			case Statut::ACTION_FAIT_DELAI:
				if($actionStatutCode === Statut::VALIDER)
				{
					$NextStep = Statut::ACTION_SOLDEE_DELAI;
					$subject = 'Action Soldée';
					$commentaire = 'Le ' . $now . '. L\'action '.$action->getLibelle().' a été soldée par '. $animateur .' , animateur de l\'instance ';
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$subject = 'Action non soldée';
					$commentaire = 'Le ' . $now .', '. $animateur . ' a qualifié l\'action initulé : '.$action->getLibelle().' de non efficace.
									L\'action n\'est donc pas soldé.';
				}
				break;
			case Statut::ACTION_FAIT_HORS_DELAI:
				if($actionStatutCode === Statut::VALIDER)
				{
					$NextStep = Statut::ACTION_SOLDEE_HORS_DELAI;
					$subject = 'Action Soldée';
					$commentaire = 'Le ' . $now . '. L\'action '.$action->getLibelle().' a été soldée par '. $animateur .' , animateur de l\'instance ';
				}
				elseif ($actionStatutCode === Statut::INVALIDER)
				{
					$subject = 'Action non soldée';
					$commentaire = 'Le ' . $now .', '. $animateur . ' a qualifié l\'action initulée : '.$action->getLibelle().' de non efficace.
									L\'action n\'est donc pas soldé.';
				}
			break;
			case Statut::ACTION_NON_ECHUE:
				if(($actionStatutCode === Statut::ACTION_FAIT_DELAI) || ($actionStatutCode === Statut::ACTION_FAIT_HORS_DELAI))
				{
					$NextStep = '';
					$subject = 'SUPER: Fin de traitement de l\'action';
					$commentaire = 'Le ' . $now . ' . L\'action intitulée : ' . $action->getLibelle () . ' a été complètement traité par ' . $porteur . ' 
									qui demande une validation. ' . $animateur . ' est invité à se connecter pour donner une suite à cette demande .';
				}
				elseif ($actionStatutCode === Statut::EVENEMENT_DEMANDE_ABANDON)
				{
					$NextStep = '';
					$subject = 'SUPER: Demande d\'abandon de l\'action .';
					$commentaire = 'Le ' . $now . ', ' . $porteur . ' porteur désigné dans le traitement de l\'action '.$action->getLibelle().' a effectué une demande d\'abandon
							    	de l\'action pour les raisons suivantes : '.$actionStatut->getCommentaire().'. '.$animateur.' animateur de l\'instance est invité à se connecter pour donner une suite à cette demande. ';
				}
				elseif ($actionStatutCode === Statut::EVENEMENT_DEMANDE_DE_REPORT)
				{
					$NextStep = '';
					$subject = 'SUPER: Demande de report de l\'action .';
					$commentaire = 'Le ' . $now . ', ' . $porteur . ' porteur désigné dans le traitement de l\'action '.$action->getLibelle().'
						        a effectué une demande de report d\'échéance. Voici les détails de la demande : '.$actionStatut->getCommentaire().'. '.$animateur.'
								animateur de l\'instance est invité à se connecter pour donner une suite à cette demande. ';
				}
			break;
		}
		
		return array('step' =>$NextStep, 'subject' => $subject, 'commentaire' => $commentaire);
	}
	
	public static  function updateDelai($entityManager, $action) 
	{
		$allActionReport = $entityManager->getRepository ( "OrangeMainBundle:ActionReport" )->findByAction($action->getId());
		if(!empty($allActionReport))
		{
			$dateReport = $allActionReport[count($allActionReport) - 1]->getDate();
		}
		$action = $entityManager->getRepository("OrangeMainBundle:Action")->find($action->getId());
		$action->setDateCloture($dateReport);
		$entityManager->persist($action);
		$entityManager->flush();
	}
	
	public static function isLastManagerValidation($entityManager, $entity) 
	{
		$managerStatutAction = $entityManager->getRepository ( 'OrangeMainBundle:ActionStatut' )->getLastManagerValidation ( $entity->getAction ()->getId () );
		$niveauValidationParametre = $entity->getAction ()->getPorteur ()->getStructure ()->getBuPrincipal ()->getNiveauValidation ();
		$niveauManagerStatutAction = $managerStatutAction->getUtilisateur ()->getStructure ()->getTypeStructure ()->getNiveau ();
	
		if (($niveauManagerStatutAction === $niveauValidationParametre) || ($niveauManagerStatutAction === 4)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function getManagerSup($entityManager, $entity)
	{
		$structureManagerSuperieur = $entity->getUtilisateur ()->getStructure ()->getParent ();
		$user = $entityManager->getRepository ( 'OrangeMainBundle:Utilisateur' )->findOneBy ( array (
				'structure' => $structureManagerSuperieur->getId (),
				'manager' => true
		));
		
		return $user;
	}
	
}
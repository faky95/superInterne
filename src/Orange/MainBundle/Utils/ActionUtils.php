<?php
namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\Tache;
use Orange\MainBundle\Entity\TacheStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\ActionGeneriqueHasStatut;

class ActionUtils {
	
	public static function updateDocument($entityManager, $action, $entity, $user) {
		$entity->getErq()->setAction($action);
		$entity->getErq()->setUtilisateur($user);
		$entityManager->persist($entity->getErq());
		$entityManager->flush();
	}
	
	public static function changeStatutAction($entityManager, $action, $statut, $utilisateur, $commentaire) {
		$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
		$statutAction = new ActionStatut();
		$statutAction->setAction($action);
		$statutAction->setStatut($statutEntity);
		$statutAction->setUtilisateur($utilisateur);
		$statutAction->setCommentaire($commentaire);
			
		$entityManager->persist($statutAction);
		$entityManager->flush();
	}
	
	public static function changeStatutActionGenerique($entityManager, $action, $statut, $utilisateur, $commentaire) {
		$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
		$statutAction = new ActionGeneriqueHasStatut();
		$statutAction->setActionGenerique($action);
		$statutAction->setStatut($statutEntity);
		$statutAction->setUtilisateur($utilisateur);
		$statutAction->setCommentaire($commentaire);
			
		$entityManager->persist($statutAction);
		$entityManager->flush();
	}
	
	/**
	 * @param \Orange\QuickMakingundle\Model\EntityManager $entityManager
	 * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
	 * @param string $statut
	 * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
	 * @param string $commentaire
	 */
	public static function majStatutAction($entityManager, $actionStatut, $statut, $utilisateur, $commentaire) {
		$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
		$actionStatut->setStatut($statutEntity);
		$actionStatut->setCommentaire($actionStatut->getCommentaire());
	}
	
	public static function setActionStatut($entityManager, $action, $statut, $utilisateur, $commentaire) {
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut));
		$entity =  new ActionStatut();
		$entity->setStatut($statutEntity);
		$entity->setCommentaire($commentaire);
		$entity->setUtilisateur($utilisateur);
		$entity->setDateFinExecut(new \DateTime());
		$entity->setAction($action);
		$entityManager->persist($entity);
		$entityManager->flush();
		
	}
	
	public static function majStatutActionForReport($entityManager, $actionStatut, $statut, $utilisateur) {
		//$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut));
		$actionStatut->setStatut($statutEntity);
		$actionStatut->setCommentaire($actionStatut->getCommentaire());
	}
	public static function changeStatutTache($entityManager, $tache, $statut, $utilisateur, $commentaire)
	{
		$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_ACTION);
		$statutEntity = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
		
		$statutTache = new TacheStatut();
		$statutTache->setTache($tache);
		$statutTache->setStatut($statutEntity);
		$statutTache->setUtilisateur($utilisateur);
		$statutTache->setCommentaire($commentaire);
			
		$entityManager->persist($statutTache);
		$entityManager->flush();
	}
	
	public static function setCommentaire($entityManager, $entity, $commentaire)
	{
		$entity->setCommentaire($commentaire);
		$entityManager->persist($entity);
		$entityManager->flush();
	}

	public static function reportDelai($entityManager, $entity) {
		$dateReport = $entity->getAction()->setDateCloture($entity->getDate());
		$entityManager->persist($entity->getAction());
		$entityManager->flush();
	}
	
	// Calcul de la différence entre deux dates, retourne 
	//un array donnant le nombre de jour, de minutes et de secondes restantes
	public static function dateDiff($dateSup, $dateInf) {
		
			$dateSup = strtotime($dateSup);
			$dateInf = strtotime($dateInf);
			
			$diff = abs($dateSup - $dateInf); 
			$retour = array();
			
			$tmp = $diff;
			$retour['second'] = $tmp % 60;
			
			$tmp = floor(($tmp - $retour['second']) /60);
			$retour['minute'] = $tmp % 60;
			
			$tmp = floor(($tmp - $retour['minute'])/60);
			$retour['hour'] = $tmp % 24;
			
			$tmp = floor(($tmp - $retour['hour'])  /24);
			$retour['day'] = $tmp;
			
			return $retour;
	}
	
	public static function createTache($entityManager, $dateDebut, $periodicite, $nombreTache, $actionCyclique)
	{
		for($key=0; $key<$nombreTache; $key++) {
			$entity = new Tache();
			$debut = self::addDate(new \DateTime($dateDebut), $periodicite*($key));
			$cloture = self::addDate(new \DateTime($dateDebut), $periodicite*($key+1));
			$entity->setDateDebut($debut);
			$entity->setDateCloture($cloture);
			$entity->setActionCyclique($actionCyclique);
			$entity->setEtatCourant(Statut::TACHE_NON_ECHUE_NON_SOLDE);
			
			$entityManager->persist($entity);
			$entityManager->flush();
// 			ActionUtils::changeStatutTache($entityManager, $entity, Statut::TACHE_NON_ECHUE_NON_SOLDE, $actionCyclique->getAction()->getPorteur(), "Tâche non échue non soldé .");
		}
	}
		
	public static function addDate($date, $nombreJour) {
		return $date->add(new \DateInterval('P'.$nombreJour.'D'));
	}

	public static function random($taille) {
			$string = "";
			$chaine = "genevalueuniqueforrefernce";
			srand((double)microtime()*1000000);
			for($i=0; $i<$taille; $i++) {
				$string .= $chaine[rand()%strlen($chaine)];
			}
			return $string;
	}
	public static function  updateEtatReport($entityManager, $entity, $etatReel) {
		$entity->setEtatReel($etatReel);
		$entityManager->persist($entity);
		$entityManager->flush();
	}
	public static function updateEtatCourantEntity($entityManager, $entity, $codeStatut, $etatReel) {
		$entity->setEtatCourant($codeStatut);
		if($etatReel != Statut::ACTION_DEMANDE_ABANDON && $etatReel != Statut::ACTION_DEMANDE_REPORT)
		   $entity->setEtatReel($etatReel);
		$entityManager->persist($entity);
		$entityManager->flush();
	}
	public static function getEmailGestionnaire($em, $entity) {
		$array = array();
		foreach($entity->getMembreEspace() as $membre) {
			if($membre->getIsGestionnaire() == 1) {
				array_push($array, $membre->getUtilisateur()->getEmail());
			}
		}
		return $array;
	}
	public static function getEmailPorteur($em, $entity) {
		return $entity->getPorteur()->getEmail();
	}
	
	public static function getAllEmailAnimateur($em, $entity) {
		$AllEmailAnimateur = array();
		$instance = $entity->getInstance();
		if($entity->getInstance()) {
			foreach($instance->getAnimateur() as $animateur) {
				array_push($AllEmailAnimateur, $animateur->getUtilisateur()?$animateur->getUtilisateur()->getEmail():NULL);
			}
		}
		return $AllEmailAnimateur;
	}
	
	public static function getEmailAnimateur($em, $entity) {
		$emailAnimateur = array();
		$allActionStatut = $em->getRepository("OrangeMainBundle:ActionStatut")->findByAction($entity->getId());
		
		$animateur = $allActionStatut[0]->getUtilisateur();
		if($animateur) {
			array_push($emailAnimateur, $animateur->getEmail());
		}
		return $emailAnimateur;
	}
	//jkkk
	public static function getEmailManager($em, $entity) {
		$emailManager = array();
		$porteur = $entity->getPorteur();
		$manager = $porteur->getSuperior();
		if($manager) {
			array_push($emailManager, $manager->getEmail());
		}
		return $emailManager;
	}
	public static function getEmailContributeur($em, $entity) {
		$email = array();
		$contributeur = $entity->getContributeur();
		if($contributeur) {
			foreach( $contributeur as $one) {
				array_push( $email, $one->getUtilisateur()->getEmail());
			}
		}
		return $email;
	}
	public static function getEmailCopy($em, $entity) {
		$emailCopy = array();
		$contributeur = $entity->getContributeur();
		if($contributeur) {
			foreach( $contributeur as $one) {
				array_push( $emailCopy, $one->getUtilisateur()->getEmail());
			}
		}
		$groupe = $entity->getGroupe();
		if($groupe) {
			foreach( $groupe as $one) {
				foreach( $one->getMembreGroupe() as $oneMembre) {
					array_push( $emailCopy, $oneMembre->getUtilisateur()->getEmail());
				}
			}
		}
		return $emailCopy;
	}
	
	public static function getActionMembresEmail($em, $entity) {
		$membreEmail = array();
		$allActionStatut = $em->getRepository("OrangeMainBundle:ActionStatut")->findByAction($entity->getId());
		$lastActionStatut = $allActionStatut[count($allActionStatut) - 1];
		$porteur = $entity->getPorteur();
		array_push($membreEmail, $porteur->getEmail());
		$contributeur = $entity->getContributeur();
		if($contributeur) {
			foreach( $contributeur as $one) {
				array_push( $membreEmail, $one->getUtilisateur()->getEmail());
			}
		}
		$groupe = $entity->getGroupe();
		if($groupe) {
			foreach( $groupe as $one) {
				foreach( $one->getMembreGroupe() as $oneMembre) {
					array_push( $membreEmail, $oneMembre->getUtilisateur()->getEmail());
				}
			}
		}
		$manager = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy( array(
				'structure' => $porteur->getStructure()->getId(), 'manager' => true
			));
		if($manager) {
			array_push($membreEmail, $manager->getEmail());
		}
		$animateur = $allActionStatut[0]->getUtilisateur();
		if($animateur) {
			array_push($membreEmail, $animateur->getEmail());
		}
		return $membreEmail;
	}
	
	public static function setReferenceAction($em, $action) {
		$action->setReference('A_'.$action->getId());
		$em->persist($action);
		$em->flush();
	}
	
	public static function setReferenceActionSignalisation($em, $action, $signalisation) {
		$action->setReference('S_'.$signalisation->getId().'-'.'A_'.$action->getId());
		$em->persist($action);
		$em->flush();
	}
	public static function setReferenceActionSignalisationBis($em, $action, $signalisation) {
		$action->setReference('S_'.$signalisation->getId().'-'.'A_'.$action->getId());
		$em->persist($action);
	}
	
	public static function setReferenceActionGenerique($em, $action) {
		$action->setReference('AG_'.$action->getId());
		$em->persist($action);
		$em->flush();
	}
	
}
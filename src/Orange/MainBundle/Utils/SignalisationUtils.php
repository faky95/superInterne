<?php

namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\SignalisationAnimateur;
use Orange\MainBundle\Entity\SignalisationReformulation;
use Orange\MainBundle\Entity\Signalisation;

class SignalisationUtils {
	
	/**
	 * 
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
	 * @param string $statut
	 * @param \Orange\MainBundle\Entity\Signalisation $signalisation
	 * @param string $commentaire
	 */
	public static function changeStatutSignalisation($entityManager, $utilisateur, $statut, $signalisation, $commentaire )
	{
		$typeStatut = $entityManager->getRepository('OrangeMainBundle:TypeStatut')->findOneByLibelle(TypeStatut::TYPE_SIGNALISATION);
		$statutSignalisation = $entityManager->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code' => $statut, 'typeStatut' => $typeStatut->getId()));
		
		$signalisationStatut = new SignalisationStatut();
		$signalisationStatut->setCommentaire($commentaire);
		$signalisationStatut->setDateStatut(new \DateTime());
		$signalisationStatut->setSignalisation($signalisation);
		$signalisationStatut->setStatut($statutSignalisation);
		$signalisationStatut->setUtilisateur($utilisateur);
		$entityManager->persist($signalisationStatut);
		$entityManager->flush();
	}
	
	public static function addAnimateur($entityManager, $utilisateur, $signalisation)
	{
		$signalisationAnimateur = new SignalisationAnimateur();
		self::updateOtherAnimateurState($entityManager, $signalisation);
		$signalisationAnimateur->setActif(true);
		$signalisationAnimateur->setUtilisateur($utilisateur);
		$signalisationAnimateur->setSignalisation($signalisation);
		
		$entityManager->persist($signalisationAnimateur);
		$entityManager->flush();
	}
	
	public static function updateOtherAnimateurState($entityManager, $signalisation)
	{
		
		$listeAnimateurs = $entityManager->getRepository('OrangeMainBundle:SignalisationAnimateur')->findBy(array('signalisation' => $signalisation->getId(), 'actif' => true));
		
		if(!empty($listeAnimateurs)){
			foreach ($listeAnimateurs as $animateur){
				$animateur->setActif(false);
				$entityManager->flush();
			}
		}
	}
	
	public static function getSignalisationMembresEmail($entityManager, $signalisation)
	{
		$membreEmail = array();
		$source = $signalisation->getSource()->getUtilisateur();
		array_push ($membreEmail, $source->getEmail());
		
		$animateur = $entityManager->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('actif' => true, 'signalisation' => $signalisation->getid()));
		
		if($animateur){
			array_push($membreEmail, $animateur->getUtilisateur()->getEmail());
			
			$structureAnimateur = $animateur->getUtilisateur()->getStructure();
			$managerAnimateur = $entityManager->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('structure' => $structureAnimateur->getid(), 'manager' => true));
			
			if($managerAnimateur){
				array_push($membreEmail, $managerAnimateur->getEmail());
			}
		}
		
		return $membreEmail;
	}
	
	public static function getSignalisationRejeteMembresEmail($entityManager, $signalisation)
	{
		$membreEmail = array();
		$source = $signalisation->getSource()->getUtilisateur();
		$instance = $signalisation->getInstance();
		
		array_push ($membreEmail, $source->getEmail());
		
		$animateur = $entityManager->getRepository('OrangeMainBundle:SignalisationAnimateur')->findOneBy(array('actif' => true, 'signalisation' => $signalisation->getid()));
		
		$animateurs = $instance->getAnimateur()->count()==0
		? $instance->getParent()->getAnimateur()
		: $instance->getAnimateur();
		foreach ($animateurs as $animateur){
			array_push ($membreEmail, $animateur->getUtilisateur()->getEmail());
		}
		return $membreEmail;
	}
	
	public static function setReferenceSignalisation($em, $signalisation)
	{
		$signalisation->setReference('S_'.$signalisation->getId());
		$em->persist($signalisation);
		$em->flush();
	}
	/**
	 * 
	 * @param unknown $entityManager
	 * @param unknown $utilisateur
	 * @param Signalisation $signalisation
	 */
	
	static function createReformulationSignalisation($entityManager, $utilisateur, $signalisation )
	{
		$reformulation = new SignalisationReformulation();
		$reformulation->setLibelle($signalisation->getLibelle());
		$reformulation->setDescription($signalisation->getDescription());
		$reformulation->setInstance($signalisation->getInstance());
		$reformulation->setDomaine($signalisation->getDomaine());
		$reformulation->setSite($signalisation->getSite());
		$reformulation->setSource($signalisation->getSource());
		$reformulation->setTypeSignalisation($signalisation->getTypeSignalisation());
		$reformulation->setSignalisation($signalisation);
	
		$entityManager->persist($reformulation);
		$entityManager->flush();
	}
}
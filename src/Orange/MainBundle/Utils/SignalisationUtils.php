<?php

namespace Orange\MainBundle\Utils;

use Orange\MainBundle\Entity\SignalisationStatut;
use Orange\MainBundle\Entity\TypeStatut;
use Orange\MainBundle\Entity\SignalisationAnimateur;

class SignalisationUtils {
	
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
}
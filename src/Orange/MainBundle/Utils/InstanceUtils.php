<?php
namespace Orange\MainBundle\Utils;

class InstanceUtils 
{
	
	public static function animateursEmail($entityManager, $instance) {
		$membreEmail = array();
		$animateurs = $entityManager->getRepository('OrangeMainBundle:Animateur')->findByInstance($instance->getId());
		if(!empty($animateurs)) {
			foreach ($animateurs as $one) {
				array_push($membreEmail, $one->getUtilisateur()->getEmail());
			}
		}
		return $membreEmail;
	}
	public static function animateursComplet($entityManager, $instance) {
		$membreEmail = array('email'=>array(), 'nom'=>array());
		$animateurs = $entityManager->getRepository('OrangeMainBundle:Animateur')->findByInstance($instance->getId());
		if(!empty($animateurs)) {
			foreach ($animateurs as $one) {
				array_push($membreEmail['email'], $one->getUtilisateur()->getEmail());
				array_push($membreEmail['nom'], $one->getUtilisateur()->getCompletNom());
			}
		}
		return $membreEmail;
	}
}
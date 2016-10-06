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
}
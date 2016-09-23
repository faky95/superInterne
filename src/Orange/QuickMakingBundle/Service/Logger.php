<?php

namespace Orange\QuickMakingBundle\Service;

Class Logger 
{
	public function log($container, $logger, $user, $descAction)
	{
		$login = $user ? $user->getUsername() : 'inconnu';
		$logger->info('['.date('Y-m-d H:i:s').'] trace '.$descAction.' | '.$user->getPrenom().' '.$user->getNom().' | '.$login.' | '.
				$container->get('request')->getClientIp().' | '.$_SERVER['REQUEST_URI']);
	}
}
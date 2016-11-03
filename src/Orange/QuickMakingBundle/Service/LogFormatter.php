<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\QuickMakingBundle\Service;

use Monolog\Formatter\FormatterInterface;

class LogFormatter implements FormatterInterface
{
	public function format(array $record)
	{
		$container = $record['context']['container'];
		$token	= $container->get('security.context')->getToken();
		$user	= $token ? $token->getUser() : null;
		$login 		= $user ? $user->getUsername() : 'inconnu';
		$request	= $container->get('request');
		
		if(isset($record['context']['request_uri'])){
		   $line  = "";
		} else {
			$space = '			';
			$line = sprintf("[%s]_____________________________________	%s", date('Y-m-d H:i:s'), $record['message']).PHP_EOL.$space.
				sprintf("			|____ Nom d'utilisateur	__________	%s", $login).PHP_EOL.$space.
				sprintf("			|____ Adresse IP		__________	%s", $container->get('request')->getClientIp()).PHP_EOL.$space.
				sprintf("			|____ Route				__________	%s", $request->get('_route')).PHP_EOL.$space.
				sprintf("			|____ Paramètres		__________	%s", json_encode($request->get('_route_params'))).PHP_EOL.$space.
				sprintf("			|____ URL				__________	%s", $request->getUri()).PHP_EOL.$space;
		}
		if($request->getMethod()=='POST') {
			$line .= sprintf("			|____ Données postées	__________	%s", json_encode($request->request->all())).PHP_EOL;;
		}
		return $line.PHP_EOL.PHP_EOL;
	}

	public function formatBatch(array $records)
	{
		foreach ($records as $key => $record) {
			$records[$key] = $this->format($record);
		}

		return $records;
	}
}
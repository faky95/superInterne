<?php

namespace Orange\MainBundle\Utils;

class LogsMailUtils{
	
	static function LogOnFileMail($isSend, $subject, $to,$cc=null,$nbActions=null){
		$to= implode($to,'|');
		$cc= $cc ? implode($cc,'|'):'aucun';
		$content='';
		$date = new \DateTime();
		$dossier = __DIR__."/../../../../web/upload/logs/".date("Y_m_d");
		$content .= "Objet: ".$subject.PHP_EOL;
		$content .= "A: ".$to.PHP_EOL;
		$content .= "Cc: ".$cc.PHP_EOL;
		if(!empty($nbActions))
			$content .= "Nombre d'actions: ".$nbActions.PHP_EOL;
		$content .= "Date: ".$date->format("d/m/Y h:i:s").PHP_EOL;
		$content .= "Envoyé: ". ($isSend? "Oui":"Non").PHP_EOL;
		$content .= "-------------------------------------------------------------------------------".PHP_EOL;
		if(!file_exists($dossier))
			mkdir($dossier, 0777, true);
		$file   = $subject.".txt";
		$chemin = $dossier."/".$file;
		file_put_contents($chemin,$content,FILE_APPEND | LOCK_EX);
		return $chemin;
	}
}
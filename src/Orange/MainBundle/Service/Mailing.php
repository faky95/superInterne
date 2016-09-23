<?php

namespace Orange\MainBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use FOS\UserBundle\Mailer\Mailer;
class Mailing {
	protected $mailer;
	protected $templating;
	private $from = "orange@orange.sn";	
	private $name = "Basiques des Réseaux";

	public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
	{
		$this->mailer = $mailer;
		$this->templating = $templating;
	}
	
	public function send($to, $subject, $body) {
		$mail = \Swift_Message::newInstance();
		$mail->setFrom(array($this->from => $this->name))
		->setTo($to)
		->setSubject($subject)
		->setBody($body)
		->setContentType('text/html');
		return $this->mailer->send($mail);
	}
	
	public function mailRejet($acteur, $groupe, $content, $suivi){
		$to = $acteur->getEmail();
		$subject = "Rejet de suivi";
		$mail = \Swift_Message::newInstance();
		$mail->setFrom(array($this->from => $this->name))
		->setTo($to)
 		->setCc($groupe)
		->setSubject($subject)
		->setBody(
				$this->templating->render(
						'OrangeMainBundle:Suivi:mailRejet.html.twig',
						array('body' => $content
								,'user' => $acteur
								,'suivi' => $suivi
								)))
								->setContentType('text/html');
		return $this->mailer->send($mail);
	}
}

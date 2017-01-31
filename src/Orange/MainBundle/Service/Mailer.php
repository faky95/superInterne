<?php
namespace Orange\MainBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class Mailer
{
	protected $mailer;
    protected $templating;
    private $from = "orange@orange.sn";
    private $name = " SUPER";
    
    public function __construct($mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }
    
    public function notifNewAction($to, $data){
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject("Nouvelle Action")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:nouvelleAction.html.twig', array('data' => $data)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function notifNewTache($to, $data) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject("Nouvelle tache")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:nouvelleTache.html.twig', array('tache' => $data)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function notifNewSignalisation($to, $cc, $data){
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($to)
	    	->setSubject("Nouvelle Signalisation")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:nouvelleSignalisation.html.twig', array('data' => $data)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function notifNewUser($to, $cc, $data){
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject("Création de compte")
	    	->setBody($this->templating->render('OrangeMainBundle:Utilisateur:email.html.twig', array('user' => $data)))
			->setContentType('text/html')
    		->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function send($to, $cc = null, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($body)
	    	->setContentType('text/html')
	    	->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function sendBug($to, $cc = null, $subject, $body,$chemin,$file) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject($subject)
	    	->setBody($body)
	    	->setContentType('text/html')
	    	->setCharset('utf-8')
	    	->attach(\Swift_Attachment::fromPath($chemin.'/'.$file));
    	return $this->mailer->send($mail);
    }
    
    public function sendNotifReport($to, $report, $user) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject("Création de reporting")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notifReporting.html.twig', array('body' => $report, 'user' => $user)))
			->setContentType('text/html')
			->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function sendReport($to, $subject, $file) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
			->setBcc(array('madiagne.sylla@orange-sonatel.com', 'mamekhady.diouf@orange-sonatel.com'))
	    	->setSubject($subject)
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:reporting.html.twig'))
			->setContentType('text/html')
			->setCharset('utf-8')
    		->attach(\Swift_Attachment::fromPath('./web/upload/reporting/'.$file));
    	return $this->mailer->send($mail);
    }
    
    public function sendRelanceNewAction($to, $cc, $subject, $body){
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject($subject)
	    	->setBody($this->templating->render('OrangeMainBundle:Relance:relanceNewAction.html.twig', array(
	    			'url' => $body['accueil_url'], 'action' => $body['action']
    			))
	    	)->setContentType('text/html')
    		->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function registration($user){
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($user->getEmail())
	    	->setSubject('Création de compte')
	    	->setBody($this->templating->render('OrangeMainBundle:Utilisateur:registration.html.twig', array('url' => $user)))
			->setContentType('text/html')
    		->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function sendAlerteQuartTime($to, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
    	->setTo($to)
    	->setSubject($subject)
    	->setBody($body)
    	->setContentType('text/html');
    	return $this->mailer->send($mail);
    }
    
    public function sendRappel($to, $cc = null, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject($subject)
	    	->setBody($body)
	    	->setContentType('text/html');
    	return $this->mailer->send($mail);
    }
    
    public function NotifActionEspace($to, $cc, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notifActionEspace.html.twig', array('body' => $body)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function NotifAction($to, $cc, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:newAction.html.twig', array('body' => $body)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function NotifActionSignalisation($to,$cc,$subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:newAction.html.twig', array('body' => $body)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function NotifWithCopy($to, $cc, $subject, $body, $motif=null) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject($subject)
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notif.html.twig', array('body' => $body, 'motif' => $motif)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }

    public function Notif($to, $subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notif.html.twig', array('body' => $body)))
			->setContentType('text/html')
			->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
    public function NotifUpdatePorteur($to, $data) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject("Affectation d'une action")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notifUpdatePorteur.html.twig', array('data' => $data)))
			->setContentType('text/html')
			->setCharset('utf-8');
		return $this->mailer->send($mail);
    }
    
    public function sendLogsMail($subject, $body,$chemin) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo(array("madiagne.sylla@orange-sonatel.com","mamekhady.diouf@orange-sonatel.com"))
	    	->setSubject($subject)
	    	->setBody($body)
	    	->setContentType('text/html')
	    	->setCharset('utf-8')
	    	->attach(\Swift_Attachment::fromPath($chemin));
    	return $this->mailer->send($mail);
    }
    
    
}
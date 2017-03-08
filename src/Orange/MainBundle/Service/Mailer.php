<?php
namespace Orange\MainBundle\Service;

use Symfony\Component\Templating\EngineInterface;

class Mailer
{
	protected $mailer;
    protected $templating;
    private $from = "orange@orange.sn";
    private $bcc = array("madiagne.sylla@orange-sonatel.com", "mamekhady.diouf@orange-sonatel.com");
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
    
    /**
     * @param array $to
     * @param array $copy
     * @param \Orange\MainBundle\Entity\Tache $tache
     */
    public function notifNewTache($to, $copy, $tache) {
    	$mail = \Swift_Message::newInstance();
    	$manager = $tache->getActionCyclique()->getAction()->getPorteur()->getSuperior();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
 	    	->setBcc(array('madiagne.sylla@orange-sonatel.com', 'mamekhady.diouf@orange-sonatel.com'))
	    	->setSubject("Nouvelle tache")
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:nouvelleTache.html.twig', array('tache' => $tache)))
			->setContentType('text/html')
			->setCharset('utf-8');
    	if($manager) {
    		$copy = array_merge($copy, array($manager->getEmail()));
    	}
    	$mail->setCc($copy);
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
    
    public function send($to, $cc = null, $subject, $body, $trace = false) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject(utf8_encode($subject))
	    	->setBody($body)
	    	->setContentType('text/html')
	    	->setCharset('utf-8');
    	if($trace) {
    		$mail->setBcc($this->bcc);
    	}
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
    
    public function sendAlerteQuartTime($to, $subject, $body, $trace=false) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setSubject($subject)
	    	->setBody($body)
	    	->setContentType('text/html');
    	if($trace) {
    		$mail->setBcc($this->bcc);
    	}
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
    
    public function NotifWithCopy($to, $cc, $subject, $body, $motif=null, $trace=false) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
	    	->setTo($to)
	    	->setCc($cc)
	    	->setSubject($subject)
	    	->setBody($this->templating->render('OrangeMainBundle:Notification:notif.html.twig', array('body' => $body, 'motif' => $motif)))
			->setContentType('text/html')
			->setCharset('utf-8');
    	if($trace) {
    		$mail->setBcc(array('madiagne.sylla@orange-sonatel.com', 'mamekhady.diouf@orange-sonatel.com'));
    	}
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
    
    public function notifActionGenerique($to, $cc=null,$subject, $body) {
    	$mail = \Swift_Message::newInstance();
    	$mail->setFrom(array($this->from => $this->name))
    	->setTo($to)
    	->setCc(null)
    	->setSubject(utf8_encode($subject))
    	->setBody($this->templating->render('OrangeMainBundle:Notification:notifActionGenerique.html.twig', array('body' => $body)))
    	->setContentType('text/html')
    	->setCharset('utf-8');
    	return $this->mailer->send($mail);
    }
    
}
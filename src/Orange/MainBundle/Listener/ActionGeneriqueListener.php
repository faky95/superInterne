<?php
/**
 * @Maxady();
 */

namespace Orange\MainBundle\Listener;

use FOS\UserBundle\Mailer\Mailer;
use Orange\MainBundle\Event\ActionGeneriqueEvent;
use Orange\MainBundle\OrangeMainEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ActionGeneriqueListener implements EventSubscriberInterface
{
    
    /**
     * @var array
     */
    protected $states;
    
    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($states = array(), ContainerInterface $container, $mailer)
    {
    	$this->helper = $mailer;
    	$this->container = $container;
        $this->states 	= $states;
    }

    public static function getSubscribedEvents()
    {
        return array(
        		OrangeMainEvents::ACTIONGENERIQUE_NOUVEAU			=> 'onCreateAction',
        		OrangeMainEvents::ACTIONGENERIQUE_FAITE             => 'onFaiteAction',
        		OrangeMainEvents::ACTIONGENERIQUE_SOLDE             => 'onSoldeAction',
        		OrangeMainEvents::ACTIONGENERIQUE_ABANDON           => 'onAbandonAction'
        	);
    }

    public function onCreateAction(ActionGeneriqueEvent $event) {
    	$ext = $event->getActionGeneriqueManager()->newAction($event->getActionGenerique(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onFaiteAction(ActionGeneriqueEvent $event){
    	$ext = $event->getActionGeneriqueManager()->faiteAction($event->getActionGenerique(), $this->helper);
    	return isset($ext) ? $ext : false;
    	
    }
    
    public function onSoldeAction(ActionGeneriqueEvent $event){
    	$ext = $event->getActionGeneriqueManager()->solderAction($event->getActionGenerique(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onAbandonAction(ActionGeneriqueEvent $event){
    	$ext = $event->getActionGeneriqueManager()->abandonAction($event->getActionGenerique(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
   
}

<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Orange\MainBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Orange\MainBundle\OrangeMainEvents;
use Orange\MainBundle\Event\ActionEvent;
use FOS\UserBundle\Mailer\Mailer;


/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class ActionListener implements EventSubscriberInterface
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
        		OrangeMainEvents::ACTION_CREATE_NOUVELLE			=> 'onCreateAction',
        		OrangeMainEvents::ACTION_ESPACE_CREATE_NOUVELLE		=> 'onCreateActionEspace',
        		OrangeMainEvents::ACTION_VALIDATE					=> 'onValidateAction',
        		OrangeMainEvents::ACTION_FAITE						=> 'onFaiteAction',
        		OrangeMainEvents::ACTION_CLOTURE					=> 'onClotureAction',
        		OrangeMainEvents::ACTION_PAS_SOLDER					=> 'onPasSolderAction',
        		OrangeMainEvents::ACTION_DEMANDE_ABANDON			=> 'onDemandeAbandonAction',
        		OrangeMainEvents::ACTION_DEMANDE_ABANDON_ACCEPTEE 	=> 'onAbandonAccepteeAction',
        		OrangeMainEvents::ACTION_DEMANDE_ABANDON_REFUSEE 	=> 'onAbandonRefuseeAction',
        		OrangeMainEvents::ACTION_DEMANDE_REPORT 			=> 'onDemandeReportAction',
        		OrangeMainEvents::ACTION_DEMANDE_REPORT_ACCEPTEE 	=> 'onReportAccepteeAction',
        		OrangeMainEvents::ACTION_DEMANDE_REPORT_REFUSEE 	=> 'onReportRefuseeAction',
        		OrangeMainEvents::ACTION_PROPOSITION_PORTEUR 	 	=> 'onPropositionPorteurAction',
        		OrangeMainEvents::ACTION_PROPOSITION_ANIMATEUR	 	=> 'onPropositionAnimateurAction',
        		OrangeMainEvents::ACTION_VALIDATION_ANIMATEUR	 	=> 'onValidationAnimateurAction',
        		OrangeMainEvents::ACTION_VALIDATION_MANAGER	 		=> 'onValidationManagerAction',
				OrangeMainEvents::ACTION_REASSIGNATION				=> 'onReassignationAction',
				OrangeMainEvents::ACTION_UPDATE						=> 'onUpdateAction'
        	);
    }

    public function onReassignationAction(ActionEvent $event) {
    	$ext = $event->getActionManager()->reassignationAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onCreateActionEspace(ActionEvent $event) {
    	$ext = $event->getActionManager()->createNewActionEspace($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onCreateAction(ActionEvent $event) {
    	$ext = $event->getActionManager()->createNewAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
	}
	
    
    public function onValidateAction(ActionEvent $event) {
    	$ext = $event->getActionManager()->validerAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onFaiteAction(ActionEvent $event) {
    	$ext = $event->getActionManager()->faiteAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }

    public function onClotureAction(ActionEvent $event){
    	$ext = $event->getActionManager()->clotureAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onPasSolderAction(ActionEvent $event){
    	$ext = $event->getActionManager()->pasSolderAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onDemandeAbandonAction(ActionEvent $event){
    	$ext = $event->getActionManager()->abandonAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onAbandonAccepteeAction(ActionEvent $event){
    	$ext = $event->getActionManager()->AbandonAccepteAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onAbandonRefuseeAction(ActionEvent $event){
    	$ext = $event->getActionManager()->abandonRefuseAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onDemandeReportAction(ActionEvent $event){
    	$ext = $event->getActionManager()->reportAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onReportAccepteeAction(ActionEvent $event){
    	$ext = $event->getActionManager()->reportAccepteAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onReportRefuseeAction(ActionEvent $event){
    	$ext = $event->getActionManager()->reportRefuseAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onPropositionPorteurAction(ActionEvent $event){
    	$ext = $event->getActionManager()->propositionPorteurAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onPropositionAnimateurAction(ActionEvent $event){
    	$ext = $event->getActionManager()->propositionAnimateurAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    public function onValidationAnimateurAction(ActionEvent $event){
    	$ext = $event->getActionManager()->validationAnimateurAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
     public function onValidationManagerAction(ActionEvent $event){
    	$ext = $event->getActionManager()->validationManagerAction($event->getAction(), $this->helper);
    	return isset($ext) ? $ext : false;
	}

	/**
	 * @param \Orange\MainBundle\Event\ActionEvent $event
	 */
	
	public function onUpdateAction($event) {
    	$ext = $event->getActionManager()->createUpdateAction($event->getAction());
    	return isset($ext) ? $ext : false;
    }
}

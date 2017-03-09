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
use Orange\MainBundle\Entity\ActionGenerique;
use Orange\MainBundle\Event\ActionGeneriqueEvent;


/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
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
        		OrangeMainEvents::ACTIONGENERIQUE_NOUVEAU			=> 'onCreateAction'
        	);
    }

    public function onCreateAction(ActionGeneriqueEvent $event) {
    	$ext = $event->getActionGeneriqueManager()->newAction($event->getActionGenerique(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
   
}

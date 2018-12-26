<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orange\MainBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Orange\MainBundle\Model\ActionManager;
use Symfony\Component\Security\Core\SecurityContext;
use Orange\MainBundle\Entity\Action;
use FOS\UserBundle\Event\UserEvent;

class ActionEvent extends UserEvent
{
	/**
	 * @var ActionManager
	 */
    private $actionManager;
    
    /**
     * @var \Orange\MainBundle\Entity\Action
     */
    private $action;
    
    /**
     * @var \Orange\MainBundle\Entity\ActionStatut
     */
    private $actionStatut;

    /**
     * @param SecurityContext $context
     * @param Request $request
     * @param ActionManager $am
     */
    public function __construct(SecurityContext $security_context, ActionManager $am = null)
    {
    	$this->security_context = $security_context;
        $this->actionManager = $am;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $action
     */
    public function createForAction($action) {
        $this->action = $action;
       // $this->action->setDateModification( new \DateTime('now'));
    	return $this;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     */
    public function createForActionStatut($actionStatut) {
    	$this->actionStatut = $actionStatut;
    	return $this;
    }

    /**
     * @return ActionManager
     */
    public function getActionManager()
    {
        return $this->actionManager;
    }

    /**
     * set suivi
     * @param Suivi
     */
    public function setAction(\Orange\MainBundle\Entity\Action $action) {
    	$this->action = $action;
    }
    
    /**
     * get Action
     * @return \Orange\MainBundle\Entity\Action
     */
    public function getAction() {
    	return $this->action;
    }
    
	
}

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
use Symfony\Component\Security\Core\SecurityContext;
use Orange\MainBundle\Entity\Action;
use FOS\UserBundle\Event\UserEvent;
use Orange\MainBundle\Model\ActionGeneriqueManager;

class ActionGeneriqueEvent extends UserEvent
{
	/**
	 * @var ActionGeneriqueManager
	 */
    private $actionGeneriqueManager;
    
    /**
     * @var \Orange\MainBundle\Entity\ActionGenerique
     */
    private $actionGenerique;
    
    /**
     * @var \Orange\MainBundle\Entity\ActionStatut
     */
    private $actionStatut;

    /**
     * @param SecurityContext $context
     * @param Request $request
     * @param ActionGeneriqueManager $am
     */
    public function __construct(SecurityContext $security_context, ActionGeneriqueManager $am = null)
    {
    	$this->security_context = $security_context;
        $this->actionGeneriqueManager = $am;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\ActionGenerique $action
     */
    public function createForActionGenerique($action) {
    	$this->actionGenerique = $action;
    	return $this;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionStatut
     */
    public function createForActionGeneriqueStatut($actionStatut) {
    	$this->actionStatut = $actionStatut;
    	return $this;
    }

    /**
     * 
     * @return \Orange\MainBundle\Model\ActionGeneriqueManager
     */
    public function getActionGeneriqueManager()
    {
        return $this->actionGeneriqueManager;
    }

    /**
     * set suivi
     * @param Suivi
     */
    public function setActionGenerique(\Orange\MainBundle\Entity\ActionGenerique $action) {
    	$this->actionGenerique = $action;
    }
    
    /**
     * get Action
     * @return \Orange\MainBundle\Entity\ActionGenerique
     */
    public function getActionGenerique() {
    	return $this->actionGenerique;
    }
    
	
}






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
     * @var ActionManager
     */
    private $actionManager;
    
    /**
     * @var \Orange\MainBundle\Entity\ActionGenerique
     */
    private $actionGenerique;
    
    /**
     * @var \Orange\MainBundle\Entity\ActionGeneriqueHasStatut
     */
    private $actionGeneriqueStatut;

    /**
     * @param SecurityContext $context
     * @param Request $request
     * @param ActionGeneriqueManager $am
     */
    public function __construct(SecurityContext $security_context, ActionGeneriqueManager $am = null, ActionManager $actMan)
    {
    	$this->security_context = $security_context;
        $this->actionGeneriqueManager = $am;
        $this->actionManager  = $actMan;
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
     *
     * @return \Orange\MainBundle\Model\ActionManager
     */
    public function getActionManager()
    {
    	return $this->actionManager;
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
    
    /**
     * 
     * @return \Orange\MainBundle\Entity\ActionGeneriqueHasStatut
     */
    public function getActionGeneriqueStatut(){
    	return $this->actionGeneriqueStatut;
    }
    
    /**
     * 
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueStatut
     */
    public function setActionGeneriqueStatut(\Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueStatut){
    	$this->actionGeneriqueStatut = $actionGeneriqueStatut;
    }
    
	
}






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
use FOS\UserBundle\Event\UserEvent;
use Orange\MainBundle\Model\SignalisationManager;

class SignalisationEvent extends UserEvent
{
	/**
	 * @var SignalisationManager
	 */
    private $signalisationManager;
    
    /**
     * @var \Orange\MainBundle\Entity\Signalisation
     */
    private $signalisation;
    
    /**
     * @var \Orange\MainBundle\Entity\SignalisationStatut
     */
    private $signalisationStatut;

    /**
     * @param SecurityContext $context
     * @param Request $request
     * @param SignalisationManager $sm
     */
    public function __construct(SecurityContext $security_context, SignalisationManager $sm = null)
    {
    	$this->security_context = $security_context;
        $this->signalisationManager = $sm;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     */
    public function createForSignalisation($signalisation) {
    	$this->signalisation = $signalisation;
    	return $this;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\SignalisationStatut $signalisationStatut
     */
    public function createForSignalisationStatut($signalisationStatut) {
    	$this->signalisationStatut = $signalisationStatut;
    	return $this;
    }

    /**
     * @return SignalisationManager
     */
    public function getSignalisationManager()
    {
        return $this->signalisationManager;
    }
    
    /**
     * @return \Orange\MainBundle\Entity\Signalisation
     */
	public function getSignalisation() {
		return $this->signalisation;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Signalisation $signalisation
	 * @return \Orange\MainBundle\Event\SignalisationEvent
	 */
	public function setSignalisation($signalisation) {
		$this->signalisation = $signalisation;
		return $this;
	}
	
}






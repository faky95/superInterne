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
//\\
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Repository;
use Orange\MainBundle\Controller\BaseController;
use Orange\MainBundle\Filter\IndicateurFilter;
use Orange\MainBundle\Filter\StatistiqueFilter;
use Orange\MainBundle\Filter\SuiviFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Orange\MainBundle\Entity\Statistique;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Event\SignalisationEvent;
use FOS\UserBundle\Mailer\Mailer;


/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class SignalisationListener implements EventSubscriberInterface
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
        		OrangeMainEvents::SIGNALISATION_CREATE_NOUVELLE		=> 'onCreateSignalisation',
        		OrangeMainEvents::SIGNALISATION_PRISE_EN_CHARGE     => 'onSignalisationPriseEnCharge',
        		OrangeMainEvents::SIGNALISATION_NON_PRISE_EN_CHARGE => 'onSignalisationNonPriseEnCharge',
        		OrangeMainEvents::SIGNALISATION_EFFICACE            => 'onSignalisationEfficace',
        		OrangeMainEvents::SIGNALISATION_NON_EFFICACE        => 'onSignalisationNonEfficace',
        		
        		
        	);
    }

    public function onCreateSignalisation(SignalisationEvent $event) {
    	$ext = $event->getSignalisationManager()->createNewSignalisation($event->getSignalisation(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onSignalisationPriseEnCharge(SignalisationEvent $event) {
    	$ext = $event->getSignalisationManager()->priseEnChargeSignalisation($event->getSignalisation(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onSignalisationNonPriseEnCharge(SignalisationEvent $event) {
    	$ext = $event->getSignalisationManager()->signalisationNonPriseEnCharge($event->getSignalisation(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onSignalisationEfficace(SignalisationEvent $event) {
    	$ext = $event->getSignalisationManager()->signalisationEfficace($event->getSignalisation(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
    public function onSignalisationNonEfficace(SignalisationEvent $event) {
    	$ext = $event->getSignalisationManager()->signalisationNonEfficace($event->getSignalisation(), $this->helper);
    	return isset($ext) ? $ext : false;
    }
    
}
?>

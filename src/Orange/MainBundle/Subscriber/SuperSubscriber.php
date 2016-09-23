<?php

namespace Orange\MainBundle\Subscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Utils\Notification;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Entity\Statut;

class SuperSubscriber implements EventSubscriber
{
	
	protected $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
    public function getSubscribedEvents()
    {
        return array(
        	Events::postUpdate
        );
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
    	$this->index($args);
    }
    
    
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function index(LifecycleEventArgs $args)
    {
    	
    	$entity = $args->getObject();
    	$entityManager = $args->getObjectManager();
    	$helper = $this->container->get( 'orange.main.mailer' );
    	$securityContext = $this->container->get('security.context');
    	
    	if ($entity instanceof Action) 
    	{
    		$currentUser = $securityContext->getToken()->getUser();
    		$subject 	 = " Mise à jour de l\'action";
    		$commentaire = " L\'action ".$entity->getLibelle()." a été modifié par ".$currentUser.". Ci dessous les détails post modification .";
    		$uow = $entityManager->getUnitOfWork();
    		$uow->computeChangeSets();
    		$changeset = $uow->getEntityChangeSet($entity);
            $updatedValue = array('libelle', 'dateInitial', 'instance', 'domaine', 'porteur', 'typeAction', 'priorite');
            if(!empty($changeset['libelle']) || !empty($changeset['dateInitial']) || !empty($changeset['instance']) || !empty($changeset['domaine'])
            	|| !empty($changeset['porteur']) || !empty($changeset['typeAction']) || !empty($changeset['priorite']))
            {
                Notification::postUpdate($helper, $subject, "gillesavi@gmail.com", $commentaire, $changeset, 'ACTION');
            }
            
            if(!empty($changeset['porteur']))
            {
            	ActionUtils::changeStatutAction($entityManager, $entity, Statut::NOUVELLE_ACTION, $currentUser, "Cette action a été rechargée suite à un changement du porteur ");
            	ActionUtils::updateEtatCourantEntity($entityManager, $entity, Statut::NOUVELLE_ACTION);
            }
    	}
    	elseif ($entity instanceof Signalisation)
    	{
 			   		
    	}
    }
    
}
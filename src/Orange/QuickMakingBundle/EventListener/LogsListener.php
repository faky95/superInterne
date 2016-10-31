<?php
namespace Orange\QuickMakingBundle\EventListener;

use Orange\QuickMakingBundle\Entity\Logs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Orange\QuickMakingBundle\Entity\Operation;

class LogsListener {

	protected  $container;
	
	protected  $logs = [];
	
	/**
	 * 
	 * @param ContainerInterface $container
	 */
	public function __construct($container) {
		$this->container = $container;
		
	}
	
	public function preUpdate(PreUpdateEventArgs $args) {
// 		$entity = $args->getEntity();
// 		$entityManager = $args->getEntityManager();
// 		$securityContext = $this->container->get('security.context');
// 		$user = $securityContext->getToken()?$securityContext->getToken()->getUser():null;
// 		$url = $this->container->get('request')?$this->container->get('request')->getRequestUri():null;
// 		$entityMetaData = $entityManager->getClassMetadata(get_class($entity));
// 		$nomTable =  $entityMetaData->getTableName();
		
// 		$entitiesToLogs = $this->container->hasParameter('qm_logs_entities')?
//                           $this->container->getParameter('qm_logs_entities'):
//                           array();
		
// 		if(count($entitiesToLogs)>0){
// 			foreach ($entitiesToLogs as $key => $value){
// 				$classe = $value['class'];
// 				$allColumns = array_merge($entityMetaData->getFieldNames(),array_keys($entityMetaData->getAssociationMappings()));
// 				$colonnes = count($value['columns'])>0?$value['columns']:$allColumns;
				
// 					if($entity instanceof $classe){
// 						$operation = new Operation();
// 						$operation->setUser($user);
// 						$operation->setUrl($url);
// 						$this->logs['operation']= $operation;
// 						foreach ($colonnes as $colonne){
// 							if($args->hasChangedField($colonne)){
// 								$oldValue = $this->toString($args->getOldValue($colonne));
// 								$newValue = $this->toString($args->getNewValue($colonne));
// 								$log = new Logs();	
// 								$log->loadValue($operation, get_class($entity), $nomTable, $colonne, $entity->getid(), $user->getId(), $oldValue, $newValue);
// 								$this->logs['logs'][] = $log;
// 							}
// 						}
// 					}
// 				}
// 		}
	}
	
	public function postFlush(PostFlushEventArgs $event)
	{
		// verifie si des logs ont etes creer afin de les mettre dans la base de données
		if(!empty($this->logs['logs'])) {
			$em = $event->getEntityManager();
			$em->persist($this->logs['operation']);
			foreach ($this->logs['logs'] as $log) {
				$em->persist($log);
			}
			$this->logs = [];
			$em->flush();
		}
	}

	
	public function toString($value){
		if($value instanceof  \DateTime)
			return date_format($value,'d/m/Y h:i:s');
		elseif (is_object($value) && method_exists($value, 'getId'))
			return $value->getId();
		else
		    return $value;
	}
}

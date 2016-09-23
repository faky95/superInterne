<?php

namespace Orange\QuickMakingBundle\Model;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;


class EntityManager extends \Doctrine\ORM\EntityManager
{
    /**
     * The used Parameters.
     *
     * @var Array
     */
    private $_parameters;

    public static function create($conn, Configuration $config, EventManager $eventManager = null)
    {
        $em = parent::create($conn, $config, $eventManager);
        return new EntityManager($em->getConnection(), $em->getConfiguration(), $em->getEventManager());
    }

    /**
     * Gets the repository for an entity class.
     *
     * @param string $entityName The name of the entity.
     *
     * @return \Doctrine\ORM\EntityRepository The repository class.
     */
    public function getRepository($entityName)
    {
    	$this->getConfiguration()->setDefaultRepositoryClassName('\Orange\QuickMakingBundle\Repository\EntityRepository');
        $repository = $this->getConfiguration()->getRepositoryFactory()->getRepository($this, $entityName);
        if(method_exists($repository, 'setParameters')) {
        	$repository->setParameters($this->_parameters);
        }
        return $repository;
    }
    
    public function setParameters($ids, $states, $user) {
    	$this->_parameters = array();
    	$this->_parameters['ids'] = $ids;
    	$this->_parameters['states'] = $states;
    	$this->_parameters['user'] = $user;
    }
}

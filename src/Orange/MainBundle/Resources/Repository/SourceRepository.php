<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Source;

class SourceRepository extends EntityRepository{
	
	
	
	public function allSources(){
		return $this->createQueryBuilder('s')
		->select('u.id')
		->innerJoin('s.utilisateur', 'u')
		->getQuery()
		->getResult();
	}
	
	public function findSourceBySignalisation() {
		// TODO: Auto-generated method stub	
		return $this->createQueryBuilder('q')
					->innerJoin('q.utilisateur', 'u')
					->innerJoin('q.instance', 'i')
					->where('u.id = :user_id')
					->andWhere('i.id = :instance_id')
 					->setParameters(array('user_id' => $user_id, 'instance_id' => $instance_id ))
					->getQuery()
					->getOneOrNullResult();
	}
	
	public function getAllSources() {
		// TODO: Auto-generated method stub
		return $this->createQueryBuilder('q')
		->select('u.id as user , q.id as id')
		->innerJoin('q.utilisateur', 'u')
		->getQuery()
		->execute();
	}
	
	public function allSourcesWithSignalisation(){
		return $this->createQueryBuilder('s')
		->select('u.id')
		->innerJoin('s.utilisateur', 'u')
		->innerJoin('s.signalisation', 'sign')
		->getQuery()
		->getResult();
	}
}
?>
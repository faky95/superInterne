<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\MembreEspace;

class MembreEspaceRepository extends EntityRepository{
	
	public function countMembreEspace($espace_id){
		
		return $this->createQueryBuilder('q')
					->select('COUNT(q)')
					->where('q.espace =:espace_id')
					->getQuery()
					->setParameter('espace_id', $espace_id)
					->getSingleScalarResult();
	}
	public function membreOfEspace($espace_id){
	
		return $this->createQueryBuilder('q')
		->select('q')
		->where('q.espace =:espace_id')
		->setParameter('espace_id', $espace_id)
		->getQuery()->getResult();
	}
}
?>
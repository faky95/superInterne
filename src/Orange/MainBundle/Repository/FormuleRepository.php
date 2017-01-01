<?php
namespace Orange\MainBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class FormuleRepository extends BaseRepository {
	
	public function getByBu($bu) {
		$queryBuilder= $this->createQueryBuilder('f')
					->innerJoin('f.bu', 'b')
					->where('b=:bu')->setParameter('bu', $bu)
					->andWhere('f.visibilite=:val')->setParameter('val', 1)
					->getQuery()->getArrayResult()
					;
		return $queryBuilder;
		
	}
	public function listColorOfBu($bu) {
		$queryBuilder= $this->createQueryBuilder('f')
		->select('f.couleur')
		->innerJoin('f.bu', 'b')
		->where('b=:bu')->setParameter('bu', $bu);
		return $queryBuilder->getQuery()->getResult();
	
	}
	public function listAllBu($bu) {
		$queryBuilder= $this->createQueryBuilder('f')
		->innerJoin('f.bu', 'b')
		->where('b=:bu')->setParameter('bu', $bu);
		return $queryBuilder;
	
	}
	public function getTauxStatsByBu($bu) {
		$queryBuilder= $this->createQueryBuilder('f')
			->innerJoin('f.bu', 'b')
			->where('b=:bu')->setParameter('bu', $bu)
			->andWhere('f.visibilite=:val')->setParameter('val', 1)
			->getQuery()->getArrayResult();
		return $queryBuilder;
	
	}
	
	public function getTauxStats($bu = null) {
		if ($this->_user){
			$bu=$this->_user->getStructure()->getBuPrincipal();
		}
		$queryBuilder= $this->createQueryBuilder('f')
		->innerJoin('f.bu', 'b')
		->where('b=:bu')->setParameter('bu', $bu)
		->andWhere('f.visibilite=:val')->setParameter('val', 1)
		->getQuery()->getArrayResult();
		return $queryBuilder;
	
	}
}
<?php
namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\CustomInterface\RepositoryInterface;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Entity\Priorite;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Statut;

class FormuleRepository extends BaseRepository{
	
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
		->getQuery()->getArrayResult()
		;
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
		->getQuery()->getArrayResult()
		;
		return $queryBuilder;
	
	}
}
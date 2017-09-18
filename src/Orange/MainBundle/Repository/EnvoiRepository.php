<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class EnvoiRepository extends BaseRepository{
	
	public function getEnvoi()
	{
		$queryBuilder = $this->createQueryBuilder('e')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', date('Y-m-d'))
			->getQuery()
			->execute();
		return $queryBuilder;
		
	}
	
	public function getLastsEnvoi(){
		$qb = $this->createQueryBuilder('e')
		           ->select('max(e.id) envoi_id, e envoi, r reporting')
		           ->innerJoin('e.reporting', 'r')
		           ->where('e.dateEnvoi <= :today')->setParameter('today',  date('Y-m-d'))
		           ->groupBy('r.id');
		return $qb;
	}
	
	
	public function getEnvoiStructure($bu = null, $espace = null, $projet = null)
	{
		$queryBuilder = $this->createQueryBuilder('e')
		    ->innerJoin('e.reporting', 'r')
		    ->innerJoin('r.utilisateur', 'u')
		    ->innerJoin('u.structure', 's')
			->where('e.typeReporting = 1')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', date('Y-m-d'));
		if($bu) {
			$queryBuilder->andWhere('s.buPrincipal = :bu')->setParameter('bu', $bu);
		}
	    return $queryBuilder->getQuery()->execute();
	}
	
	public function getEnvoiInstance($bu = null, $espace = null, $projet = null) {
		$date =  date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
			->innerJoin('e.reporting', 'r')
			->innerJoin('r.utilisateur', 'u')
			->innerJoin('u.structure', 's')
			->where('e.typeReporting = 2')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', $date);
		if($bu) {
			$queryBuilder->andWhere('s.buPrincipal = :bu')->setParameter('bu', $bu);
		}
		return $queryBuilder->getQuery()->execute();
	}
	
}


<?php 

namespace Orange\MainBundle\Repository;

class ContributeurRepository extends BaseRepository {
	
	public function findContributeurs($id){
		return $this->createQueryBuilder('c')
			        ->andWhere('c.action = :action')->setParameter('action', $id)
			        ->getQuery()
			        ->execute();
	}
	
	public function findContributeursForManyAction($ids){
		$qb            = $this  -> createQueryBuilder('c')
								-> select('partial c.{id}, partial u.{id, prenom, nom},partial a.{id}')
		                        -> innerJoin('c.utilisateur', 'u')
		                        -> innerJoin('c.action', 'a')
					            -> where('a.id IN (:ids)')
					            -> setParameter('ids', $ids);
		return $qb->getQuery()->execute();
	}
		
}
?>
<?php
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\Entity\Action;
use \DateTime;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\ArchitectureStructure;
use DoctrineExtensions\Query\Mysql\Date;

class ActionGeneriqueRepository extends BaseRepository {
	/**
	 * @return QueryBuilder
	 */
	public function filter() {
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.porteur', 'u')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.statut');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$queryBuilder->orWhere('u.buPrincipal=:bu')->setParameter('bu',  $this->_user->getStructure()->getBuPrincipal());
		}
		return $queryBuilder;
	}
	
	public function listAllElements($criteria) {
		$queryBuilder = $this->filter();
		$porteur = $criteria ? $criteria->getPorteur() : null;
		$toDeb = $criteria ? $criteria->hasToDebut() : null;
		$fromDeb = $criteria ? $criteria->hasFromDebut() : null;
		$toInit = $criteria ? $criteria->hasToInitial() : null;
		$fromInit = $criteria ? $criteria->hasFromInitial() : null;
		$statut = $criteria ? $criteria->hasStatut() : null;
		
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		
		if($statut) {
			$queryBuilder->andWhere('a.statut = :code')->setParameter('code', $statut->getCode());
		}
// 		if($fromDeb) {
// 			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
// 		}
// 		if($fromInit) {
// 			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
// 		}
// 		if($fromClot) {
// 			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
// 		}
		return $queryBuilder;
	}
	public function getSimpleActionByActionGenerique($id){
		$queryBuilder = $this->_em->getRepository('OrangeMainBundle:Action')->createQueryBuilder('a');
		$queryBuilder->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
			         ->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
			         ->leftJoin('a.actionGeneriqueHasAction', 'gha')
		             ->where('IDENTITY(gha.actionGenerique) = :id')->setParameter('id', $id);
		return $queryBuilder->groupBy('a.etatCourant');
		             
	}
}
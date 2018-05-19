<?php
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Instance;

class ActionGeneriqueRepository extends BaseRepository {
	/**
	 * @return QueryBuilder
	 */
	public function filter() {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('u.structure', 's')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.statut');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$queryBuilder->orWhere('s.buPrincipal=:bu')->setParameter('bu',  $this->_user->getStructure()->getBuPrincipal());
		}
		if($this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
			$queryBuilder->orWhere('u=:user')->setParameter("user", $this->_user);
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

		return $queryBuilder;
	}
	public function getStatsSimpleActionByActionGenerique($id){
		$queryBuilder = $this->_em->getRepository('OrangeMainBundle:Action')->createQueryBuilder('a');
		$queryBuilder->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
			         ->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
			         ->leftJoin('a.actionGeneriqueHasAction', 'gha')
		             ->where('IDENTITY(gha.actionGenerique) = :id')->setParameter('id', $id);
		return $queryBuilder->groupBy('a.etatCourant');
		             
	}
	/**
	 * 
	 * @param Action $criteria
	 * @param unknown $id
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByActionGenerique($criteria=null,$id){
		$queryBuilder = $this->_em->getRepository('OrangeMainBundle:Action')->createQueryBuilder('a');
		$queryBuilder
		->innerJoin('a.actionGeneriqueHasAction', 'gha')
		->where('IDENTITY(gha.actionGenerique) = :id')->setParameter('id', $id);
		if($criteria!=null){
				if($criteria->getStructure()) {
					$structure = $criteria->getStructure();
					$queryBuilder->innerJoin('a.structure', 's')->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')->andWhere('s.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
				}
				if($criteria->getDomaine()) {
					$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
				}
				if($criteria->getInstance()) {
					$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $criteria->getInstance());
				}
				if($criteria->getPorteur()) {
					$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $criteria->getPorteur());
				}
				if($criteria->getTypeAction()) {
					$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $criteria->getTypeAction());
				}
				if($criteria->getPriorite()) {
					$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $criteria->getPriorite());
				}
				if($criteria->hasStatut()) {
					$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $criteria->hasStatut()->getCode());
				}
				if($criteria->hasFromDebut()) {
					$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $criteria->hasToDebut())->setParameter('from', $criteria->hasFromDebut());
				}
				if($criteria->hasFromInitial()) {
					$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $criteria->hasToInitial())->setParameter('from', $criteria->hasFromInitial());
				}
				if($criteria->hasFromCloture()) {
					$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $criteria->hasToCloture())->setParameter('from', $criteria->hasFromCloture());
				}
		}
		return $queryBuilder;
		 
	}
	
	public function getMesStatsByActionGenerique($criteria = null){
		$queryBuilder = $this->createQueryBuilder('a')
							->leftJoin('a.porteur', 'u')
							->leftJoin('u.structure', 's')
							->leftJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.statut')
							->leftJoin('a.actionGeneriqueHasAction', 'gha')
							->leftJoin('gha.action', 'act');
		if($criteria!=null){
			if($criteria->getStructure()) {
				$structure = $criteria->getStructure();
				$queryBuilder->innerJoin('act.structure', 'str')->andWhere('str.lvl >= :level')->andWhere('str.root = :root')->andWhere('str.lft >= :left')->andWhere('str.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
			}
			if($criteria->getDomaine()) {
				$queryBuilder->andWhere('act.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
			}
			if($criteria->getInstance()) {
				$queryBuilder->andWhere('act.instance = :instance')->setParameter('instance', $criteria->getInstance());
			}
			if($criteria->getPorteur()) {
				$queryBuilder->andWhere('act.porteur = :porteur')->setParameter('porteur', $criteria->getPorteur());
			}
			if($criteria->getTypeAction()) {
				$queryBuilder->andWhere('act.typeAction = :type')->setParameter('type', $criteria->getTypeAction());
			}
			if($criteria->getPriorite()) {
				$queryBuilder->andWhere('act.priorite = :priorite')->setParameter('priorite', $criteria->getPriorite());
			}
			if($criteria->hasStatut()) {
				$queryBuilder->andWhere('act.etatReel = :code')->setParameter('code', $criteria->hasStatut()->getCode());
			}
			if($criteria->hasFromDebut()) {
				$queryBuilder->andWhere('act.dateDebut >= :from and act.dateDebut <= :to')->setParameter('to', $criteria->hasToDebut())->setParameter('from', $criteria->hasFromDebut());
			}
			if($criteria->hasFromInitial()) {
				$queryBuilder->andWhere('act.dateInitial >= :from and act.dateInitial <= :to')->setParameter('to', $criteria->hasToInitial())->setParameter('from', $criteria->hasFromInitial());
			}
			if($criteria->hasFromCloture()) {
				$queryBuilder->andWhere('act.dateCloture >= :from and act.dateCloture <= :to')->setParameter('to', $criteria->hasToCloture())->setParameter('from', $criteria->hasFromCloture());
			}
			if($criteria->instances->count()>0){
				$instIDs = array();
				foreach($criteria->instances as $val)
					$instIDs [] = $val->getId();
				$queryBuilder->andWhere('IDENTITY(act.instance) in (:insts)')->setParameter(':insts',  $instIDs);
			}
		}
		return $queryBuilder ->select("count(act.id) total ,act.etatCourant etatCourant, a.reference libelle ,a.id id")
		                     ->andWhere('u=:user')->setParameter("user", $this->_user)
		                     ->groupBy('act.etatCourant')->addGroupBy('a.id');
	}
}
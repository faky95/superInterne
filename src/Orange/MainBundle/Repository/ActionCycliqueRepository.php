<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Instance;

class ActionCycliqueRepository extends BaseRepository {
	
	/**
	 * @return QueryBuilder
	 */
	public function filter($criteria=null) {
		$queryBuilder = $this->createQueryBuilder('ac')
			->leftJoin('ac.pas', 'ps')
			->leftJoin('ac.action', 'a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->innerJoin('a.porteur', 'port')
			->leftJoin('a.priorite', 'priori')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
			->innerJoin('a.porteur', 'mp')
			->innerJoin('a.instance', 'mi')
			->leftJoin('a.contributeur', 'cont')
			->leftJoin('cont.utilisateur', 'cuser')
			->leftJoin('a.avancement', 'av')
			->leftJoin('a.structure', 'struct')
			->innerJoin('a.typeAction', 'type')
			->leftJoin('a.domaine', 'dom');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$structure_id = $this->_user->getStructure()->getRoot();
			$structure=$this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure( $this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id))));
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getAllInstances($structure)));
			
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ANIMATEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_CHEF_PROJET)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIdsForChefProjet()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_MANAGER)) {
			$structure = $this->_user->getStructure();
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure($structure)));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
			$queryBuilder->orWhere('IDENTITY(a.porteur) = :userId');
		}
		$queryBuilder->setParameter('userId', $this->_user->getId());
		$this->filterForm($queryBuilder, $criteria);
		return $queryBuilder;
	}
	
	/**
	 *
	 * @param QueryBuilder $queryBuilder
	 * @param ActionCyclique $criteria
	 */
	public function filterForm($queryBuilder, $criteria){
		$structure = $criteria && $criteria->getAction() ? $criteria->getAction()->getStructure() : null;
		$domaine = $criteria && $criteria->getAction() ? $criteria->getAction()->getDomaine() : null;
		$instance = $criteria && $criteria->getAction() ? $criteria->getAction()->getInstance() : null;
		$priorite = $criteria && $criteria->getAction() ? $criteria->getAction()->getPriorite() : null;
		$type = $criteria && $criteria->getAction() ? $criteria->getAction()->getTypeAction() : null;
		$porteur = $criteria && $criteria->getAction() ? $criteria->getAction()->getPorteur() : null;
		$toDeb = $criteria && $criteria->getAction() ? $criteria->getAction()->hasToDebut() : null;
		$fromDeb = $criteria && $criteria->getAction() ? $criteria->getAction()->hasFromDebut() : null;
		$toInit = $criteria && $criteria->getAction() ? $criteria->getAction()->hasToInitial() : null;
		$fromInit = $criteria && $criteria->getAction() ? $criteria->getAction()->hasFromInitial() : null;
		$toClot = $criteria && $criteria->getAction() ? $criteria->getAction()->hasToCloture() : null;
		$statut = $criteria && $criteria->getAction() ? $criteria->getAction()->hasStatut() : null;
		$fromClot = $criteria && $criteria->getAction() ? $criteria->getAction()->hasFromCloture() : null;
		if($criteria==null) {
			return;
		}
		if($criteria->getPas()){
			$queryBuilder->andWhere('ps = :pas')->setParameter('pas', $criteria->getPas()->getId());
		}
		if($criteria->getDayOfMonth()){
			$queryBuilder->andWhere('ac.dayOfMonth = :dOm')->setParameter('dOm', $criteria->getDayOfMonth());
		}
		if($criteria->getDayOfWeek()){
			$queryBuilder->andWhere('ac.dayOfWeek = :dOw')->setParameter('dOw', $criteria->getDayOfWeek());
		}
		if($criteria->getIteration()){
			$queryBuilder->andWhere('ac.iteration = :iteration')->setParameter('iteration', $criteria->getIteration());
		}
		if($structure) {
			$queryBuilder->innerJoin('a.structure', 's')->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')
				->andWhere('s.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())
				->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
		}
		if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
	}
	
	public function filterForExport($criteria){
		$qb = $this->filter($criteria)
			->select("partial ac.{id}, partial ps.{id, libelle}, partial domth.{id, libelle}, partial dow.{id, libelle},
					partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial},
				    partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
				    partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
				    partial port.{id, prenom, nom, structure}, partial struct.{id, service, departement, pole, direction},
				    GROUP_CONCAT(distinct av.description separator ' .__ ') avancements, 
					GROUP_CONCAT(distinct CONCAT(cuser.prenom, '  ', cuser.nom) ) contributeurs "
				);
		return $qb->leftJoin('ac.dayOfMonth', 'domth')->leftJoin('ac.dayOfWeek', 'dow')->groupBy('ac.id');
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function listAllElements() {
		$queryBuilder = $this->filter();
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
				->getQuery()->execute();
	}
	
	/**
	 * @param number $bu
	 * @param number $espace
	 * @param number $projet
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function tacheToGenerate($bu, $espace, $projet) {
		$queryBuilder = $this->createQueryBuilder('ac')
			->leftJoin('ac.tache', 't')
			->innerJoin('ac.action', 'a')
			->innerJoin('a.instance', 'i')
			->leftJoin('i.bu', 'b')
			->leftJoin('i.chantier', 'c')
			->leftJoin('i.espace', 'e')
			->andWhere('a.dateInitial < :date')
			->groupBy('ac.id')
			->having('MAX(t.dateInitial) IS NULL OR MAX(t.dateInitial) < :date')->setParameter('date', date('Y-m-d'));
		if($bu) {
			$queryBuilder->andWhere('IDENTITY(s.buPrincipal) = :bu')->setParameter('bu', $bu);
		}
		if($espace) {
			$queryBuilder->andWhere('e.id = :espace')->setParameter('espace', $espace);
		}
		if($projet) {
			$queryBuilder->andWhere('IDENTITY(c.projet) = :projet')->setParameter('projet', $projet);
		}
		return $queryBuilder->getQuery()->execute();
	}
	
	/**
	 * @param number $bu
	 * @param number $espace
	 * @param number $projet
	 * @param array $periodiciteIds
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function tacheByPeriodicite($bu, $espace, $projet, $periodiciteIds) {
		$queryBuilder = $this->createQueryBuilder('ac')
			->leftJoin('ac.tache', 't')
			->innerJoin('ac.action', 'a')
			->innerJoin('a.instance', 'i')
			->leftJoin('i.bu', 'b')
			->leftJoin('i.chantier', 'c')
			->leftJoin('i.espace', 'e')
			->groupBy('ac.id')
			->having('MAX(t.dateInitial) IS NULL OR MAX(t.dateInitial) < :date')->setParameter('date', date('Y-m-d'));
		if($bu) {
			$queryBuilder->andWhere('IDENTITY(s.buPrincipal) = :bu')->setParameter('bu', $bu);
		}
		if($espace) {
			$queryBuilder->andWhere('e.id = :espace')->setParameter('espace', $espace);
		}
		if($projet) {
			$queryBuilder->andWhere('IDENTITY(c.projet) = :projet')->setParameter('projet', $projet);
		}
		return $queryBuilder->andWhere('IDENTITY(ac.pas) IN(:periodiciteIds)')->setParameter('periodiciteIds', $periodiciteIds)
			->getQuery()->execute();
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function findWithoutTache() {
		return $this->createQueryBuilder('q')
			->leftJoin('q.tache', 't')
			->where('t.id IS NULL')
			->getQuery()->getResult();
	}
	
}
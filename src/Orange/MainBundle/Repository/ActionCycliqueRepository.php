<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\Entity\Action;
use \DateTime;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\ArchitectureStructure;

class ActionCycliqueRepository extends BaseRepository {
	
	/**
	 * @return QueryBuilder
	 */
	public function filter() {
		$queryBuilder = $this->createQueryBuilder('ac')
		->leftJoin('ac.action', 'a')
		->innerJoin('a.instance', 'insta')
		->leftJoin('insta.espace', 'espa')
		->leftJoin('a.signalisation', 'sign')
		->leftJoin('sign.source', 'src')
		->innerJoin('a.porteur', 'port')
		->leftJoin('a.priorite', 'priori')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
		->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi');
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
		if($this->_user->hasRole(Utilisateur::ROLE_SOURCE)) {
			$queryBuilder->orWhere('IDENTITY(src.utilisateur) = :userId');
		}
		return $queryBuilder->setParameter('userId', $this->_user->getId());
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
	
}
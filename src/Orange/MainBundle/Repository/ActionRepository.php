<?php
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\Entity\Action;
use \DateTime;
use Orange\MainBundle\Entity\Instance;
use DoctrineExtensions\Query\Mysql\Date;

class ActionRepository extends BaseRepository {
	
	/**
	 * @return array
	 */
	public function ActionWithStructureNull() {
		return $this->createQueryBuilder('a')
			->leftJoin('a.instance', 'inst')
			->leftJoin('a.porteur', 'port')
			->leftJoin('a.typeAction', 'typ')
			->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
			->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
			->andWhere("a.structure IS NULL")
			->getQuery()->getResult();
	}
	
	/**
	 * @return array
	 */
	public function getAllActions() {
		return $this->createQueryBuilder('a')->select('a.etatCourant AS type_statut, inst.id AS instance_id, '.date('W').' as semaine')
				->addSelect('dom.id AS domaine_id, typ.id AS type_action_id, COUNT(a.id) as nombre, port.id AS porteur_id, struct.id AS structure_id')
				->leftJoin('a.instance', 'inst')
				->leftJoin('a.porteur', 'port')
				->leftJoin('a.structure', 'struct')
				->leftJoin('a.domaine', 'dom')
				->leftJoin('a.typeAction', 'typ')
				->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
				->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
				->groupBy('type_statut, instance_id, domaine_id, type_action_id, porteur_id, structure_id')
				->getQuery()->getResult();
	}
	/**
	 * @return QueryBuilder
	 */
	public function listArchivedForExport($criteria) {
		return $this->listArchivedQueryBuilder($criteria)->select('partial i.{id, libelle, couleur}, partial pr.{id, couleur, libelle},
				partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial},
				partial d.{id, libelleDomaine}, partial ta.{id, couleur, type}, partial c.{id},
				partial cu.{id, prenom, nom},partial p.{id, prenom, nom, structure},
				partial av.{id, description}, partial s.{id, service, departement, pole, direction},
				GROUP_CONCAT(distinct av.description separator \' .__ \') avancements,
				GROUP_CONCAT(distinct CONCAT(cu.prenom, \'  \', cu.nom) ) contributeurs')
				->leftJoin('a.contributeur', 'c')
				->leftJoin('a.instance', 'i')
				->leftJoin('c.utilisateur', 'cu')
				->leftJoin('a.porteur', 'p')
				->leftJoin('a.priorite', 'pr')
				->leftJoin('a.avancement', 'av')
				->leftJoin('a.structure', 's')
				->innerJoin('a.typeAction', 'ta')
				->leftJoin('a.domaine', 'd')
				->groupBy('a.id');
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param array $criteria
	 */
	public function addCriteria($queryBuilder, $criteria, $column = array()) {
		$structure = $criteria ? $criteria->getStructure() : null;
		$domaine = $criteria ? $criteria->getDomaine() : null;
		$instance = $criteria ? $criteria->getInstance() : null;
		$priorite = $criteria ? $criteria->getPriorite() : null;
		$type = $criteria ? $criteria->getTypeAction() : null;
		$porteur = $criteria ? $criteria->getPorteur() : null;
		$toDeb = $criteria ? $criteria->hasToDebut() : null;
		$fromDeb = $criteria ? $criteria->hasFromDebut() : null;
		$toInit = $criteria ? $criteria->hasToInitial() : null;
		$fromInit = $criteria ? $criteria->hasFromInitial() : null;
		$toClot = $criteria ? $criteria->hasToCloture() : null;
		$statut = $criteria ? $criteria->hasStatut() : null;
		$fromClot = $criteria ? $criteria->hasFromCloture() : null;
		if($structure && !in_array('structure', $column)) {
			$queryBuilder->innerJoin('a.structure', 's')
				->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')->andWhere('s.rgt <= :right')
				->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())
				->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
		}
		if($domaine && !in_array('domaine', $column)) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance && !in_array('instance', $column)) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur && !in_array('porteur', $column)) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type && !in_array('type', $column)) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite && !in_array('priorite', $column)) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut && !in_array('statut', $column)) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb && !in_array('fromDeb', $column)) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit && !in_array('fromInit', $column)) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot && !in_array('fromClot', $column)) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param array $criteria
	 */
	public function listArchivedQueryBuilder($criteria) {
		$queryBuilder = $queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel');
		$this->addCriteria($queryBuilder, $criteria);
		$queryBuilder->andWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getStructure()->getBuPrincipal()->getInstanceIds()));
		return $queryBuilder->andWhere("a.etatCourant LIKE 'ABANDONNEE_ARCHIVEE' OR a.etatCourant LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\ORM\EntityRepository::findAll()
	 */
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter()->getQuery()->execute();
	}
	
	/**
	 * @param unknown $ids
	 * @return array
	 */
	public function getActions($ids) {
		return $this->createQueryBuilder('a')
			->where('a.id IN(:ids)')->setParameters(array('ids' => $ids ))
			->getQuery()->getResult();
	}
	
	public function statistiqueUtilisateur($action_liste, $all) {
		$config = $this->getEntityManager()->getConfiguration();
		$config->addCustomDatetimeFunction('WEEK', 'DoctrineExtensions\Query\Mysql\Week');
		return $this->createQueryBuilder('a')->select('a.etatCourant AS type_statut, inst.id AS instance_id, '.date('W').' as semaine, dom.id AS domaine_id, typ.id AS type_action_id, COUNT(a.id) as nombre')->leftJoin('a.instance', 'inst')->leftJoin('a.domaine', 'dom')->leftJoin('a.typeAction', 'typ')->where('a.id IN(:action_liste)')->groupBy('type_statut, instance_id, domaine_id, type_action_id')->setParameters(array(
				'action_liste' => $action_liste 
			))->getQuery()->getResult();
	}
	
	public function userToAlertRappel($bu, $espace, $projet, $states) {
		$date = date('Y-m-d');
		return $this->createQueryBuilder('a')->leftJoin('a.porteur', 'u')->where("a.etatReel LIKE 'ACTION_FAIT_DELAI' OR a.etatReel LIKE 'ACTION_FAIT_HORS_DELAI'")->
		// ->where('a.etatCourant LIKE :en_cours')
		andWhere('a.dateInitial > :date')->andWhere('a.isDeleted = 0')->addOrderBy('a.dateAction', 'DESC')->setParameter('date', $date)->
		// ->setParameter('fait', "%ACT_DEMANDE_SOLDE%")
		// ->setParameter('en_cours', "%ACT_TRAITEMENT%")
		getQuery()->execute();
	}
	
	/**
	 * @param unknown $bu
	 * @param unknown $espace
	 * @param unknown $projet
	 * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
	 */
	public function userToAlertAnimateur($bu, $espace, $projet) {
		$queryBuilder = $this->createQueryBuilder('a')->leftJoin('a.porteur', 'u')->leftJoin('a.instance', 'i')->leftJoin('i.animateur', 'an')->andWhere("a.etatReel LIKE 'ACTION_FAIT_DELAI' OR a.etatReel LIKE 'ACTION_FAIT_HORS_DELAI'")->orderBy('a.id', 'ASC')->addOrderBy('a.dateAction', 'DESC')->getQuery()->execute();
		return $queryBuilder;
	}
	
	public function alertAnimateurForReport($bu, $espace, $projet) {
		$queryBuilder = $this->createQueryBuilder('a')->leftJoin('a.porteur', 'u')->leftJoin('a.instance', 'i')->leftJoin('i.animateur', 'an')->andWhere("a.etatReel LIKE 'ACTION_DEMANDE_REPORT'")->orderBy('a.id', 'ASC')->addOrderBy('a.dateAction', 'DESC')->getQuery()->execute();
		return $queryBuilder;
	}
	public function alertAnimateurForAbandon($bu, $espace, $projet) {
		$queryBuilder = $this->createQueryBuilder('a')->leftJoin('a.porteur', 'u')->leftJoin('a.instance', 'i')->leftJoin('i.animateur', 'an')->andWhere("a.etatReel LIKE 'ACTION_DEMANDE_ABANDON'")->orderBy('a.id', 'ASC')->addOrderBy('a.dateAction', 'DESC')->getQuery()->execute();
		return $queryBuilder;
	}
	
	public function alertAnimateurGlobal($bu, $espace, $projet) {
		$queryBuilder = $this->createQueryBuilder('a' )
                     ->leftJoin('a.porteur', 'u' )
                     ->leftJoin('u.structure', 's' )
                     ->leftJoin('a.instance', 'i' )
                     ->leftJoin('i.animateur', 'an' )
                     ->where("   a.etatReel LIKE 'ACTION_DEMANDE_ABANDON' OR 
                     		       a.etatReel LIKE 'ACTION_DEMANDE_REPORT' OR 
                     		       a.etatReel LIKE 'ACTION_FAIT_DELAI' OR 
                     		       a.etatReel LIKE 'ACTION_FAIT_HORS_DELAI'" )
                     ->orderBy('a.id', 'ASC' )
                     ->addOrderBy('a.dateAction', 'DESC' );
        if($bu) {
             $queryBuilder->andWhere('IDENTITY(s.buPrincipal) = :bu')->setParameter('bu', $bu);
        }
		return $queryBuilder;
	}
	
	public function nouvelleAction($bu, $espace, $projet) {
		return $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('a.instance', 'i')
			->innerJoin('a.domaine', 'd')
			->andWhere("a.etatReel LIKE 'ACTION_NOUVELLE'")
			->orderBy('a.id', 'ASC')
			->getQuery()->execute();
	}
	
	public function userToAlertDepassement($bu, $espace, $projet) {
		$date = new \DateTime('@'.strtotime('+3 days'));
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->leftJoin('a.structure', 's')
			->innerJoin('a.instance', 'i')
			->leftJoin('i.espace', 'e')
			->innerJoin('a.domaine', 'd')
			->where('a.dateFinPrevue <= :date')
			->andWhere("a.etatCourant LIKE 'ACTION_NON_ECHUE' OR a.etatCourant LIKE 'ACTION_ECHUE_NON_SOLDEE'")
			->orderBy('a.id', 'ASC')
			->addOrderBy('a.dateAction', 'DESC')
			->setParameter('date', $date);
		if($bu) {
			$queryBuilder->andWhere('IDENTITY(s.buPrincipal) = :bu')->setParameter('bu', $bu);
		}
		if($espace) {
			$queryBuilder->andWhere('e.id = :espace')->setParameter('espace', $espace);
		}
		return $queryBuilder->getQuery()->execute();
	}
	
	public function alertQuartTime($bu, $espace, $projet) {
		$date = new \DateTime('@'.strtotime('+40 days'));
		$queryBuilder = $this->createQueryBuilder('a')
			  ->innerJoin('a.porteur', 'u')
			  ->innerJoin('a.instance', 'i')
			  ->innerJoin('u.structure', 's')
			  ->innerJoin('s.buPrincipal', 'bu')
			  ->select('a.libelle libelle, a.id id, a.reference reference, i.libelle as lib_instance,
					u.prenom as prenom, u.nom as nom, a.dateDebut as dateDebut, a.dateInitial as dateInitial, u.email as email')
			  ->where("a.etatCourant LIKE 'ACTION_NON_ECHUE'")
			  ->andWhere("a.dateFinPrevue > :date")->setParameter('date', $date)
			  ->orderBy('a.id', 'ASC')->addOrderBy('a.dateAction', 'DESC');
		if($bu) {
			$queryBuilder->andWhere('s.buPrincipal = :bu')->setParameter('bu', $bu);
		}
		return $queryBuilder->getQuery()->getArrayResult();
	}
	
	public function atteintDelai($bu, $espace, $projet) {
		return $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('a.instance', 'i')
			->innerJoin('a.domaine', 'd')
			->where('a.dateFinPrevue < CURRENT_DATE()')
			->andWhere("a.etatReel LIKE 'ACTION_ECHUE_NON_SOLDEE'")
			->orderBy('a.id', 'ASC')
			->addOrderBy('a.dateAction', 'DESC')
			->getQuery()->execute();
	}
	
	public function getActionValide($code, $criteria) {
		$queryBuilder = $this->filter()->andWhere('a.etatReel!=:code')->setParameter('code', $code);
		if(isset($criteria))
			$this->filtres($queryBuilder, $criteria, 'a');
		return $queryBuilder->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'")->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_MANAGER_ATTENTE'")->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	public function getActionValideForExport($code, $criteria) {
		$queryBuilder = $this->filterExport()->andWhere('a.etatReel!=:code')->setParameter('code', $code);
		if(isset($criteria))
			$this->filtres($queryBuilder, $criteria, 'a');
		return $queryBuilder->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'")->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_MANAGER_ATTENTE'")->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	/**
	 * Methode utilise pour charger la liste des actions
	 * 
	 * @param unknown $criteria        	
	 * @param unknown $porteur        	
	 */
	public function listAllElementsForExport($criteria, $porteur = null) {
		$queryBuilder = $this->filterExport();
		// $queryBuilder->select('partial a.{id, libelle, reference}');
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * 
	 * @param unknown $criteria        	
	 * @param unknown $porteur        	
	 */
	public function myActions($criteria, $porteur = null) {
		$queryBuilder = $this->myFilter();
		// $queryBuilder->select('partial a.{id, libelle, reference}');
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 *
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function myActionsForExport($criteria, $porteur = null) {
		$queryBuilder = $this->myFilterExport();
		// $queryBuilder->select('partial a.{id, libelle, reference}');
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * 
	 * @param unknown $criteria        	
	 * @param unknown $porteur        	
	 */
	public function listAllElements($criteria, $porteur = null) {
		$queryBuilder = $this->filter();
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * 
	 * @param unknown $criteria        	
	 * @param unknown $porteur        	
	 */
	public function listAllElementsGeneral() {
		$data = array();
		$queryBuilder = $this->filterGeneral();
		$queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
		$data = $queryBuilder->getQuery()->getArrayResult();
		$data = $this->combineTacheAndAction($data);
		return $data;
	}
	
	public function listActionsByCriteria($criteria) {
		$queryBuilder = $this->filter();
		// $queryBuilder->select('partial a.{id, libelle, reference}');
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	public function listActionsByEspace($criteria, $id, $var) {
		$queryBuilder = $this->listActionsByCriteria($criteria);
		$queryBuilder->andWhere('espa.id=:id')->setParameter('id', $id);
		if($var == false) {
			$queryBuilder->andWhere('IDENTITY(a.porteur) = :user')->andWhere('espa.id=:id')->setParameter('id', $id)->setParameter('user', $this->_user);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	public function listActionsByEspaceForExport($criteria, $id, $var, $id_user) {
		$queryBuilder = $this->filterExport();
		$queryBuilder->innerJoin('a.instance', 'i');
		if($var == false) {
			$queryBuilder->innerJoin('a.porteur', 'p')->andWhere('esp.id=:id')->setParameter('id', $id)->andWhere('p.id=:id_user')->setParameter('id_user', $id_user);
		} else {
			$queryBuilder->andWhere('esp.id=:id')->setParameter('id', $id);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	public function allActionEspace($id) {
		$queryBuilder = $this->createQueryBuilder('a');
		$queryBuilder->innerJoin('a.instance', 'i')->innerJoin('i.espace', 'e')->where('e.id=:id')->setParameter('id', $id);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")->getQuery()->execute();
	}
	public function listActionsUserByEspace($id_user, $id_espace) {
		$queryBuilder = $this->createQueryBuilder('a');
		$queryBuilder->innerJoin('a.instance', 'i')->innerJoin('a.porteur', 'p')->innerJoin('i.espace', 'e')->where('e.id=:id')->setParameter('id', $id_espace)->andWhere('p.id=:id_user')->setParameter('id_user', $id_user);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")->getQuery()->execute();
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function filterForStruct() {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->leftJoin('a.signalisation', 'sign')
			->leftJoin('sign.source', 'src')
			->innerJoin('a.porteur', 'port')
			->leftJoin('a.priorite', 'priori')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
			->innerJoin('a.porteur', 'mp')
			->innerJoin('a.instance', 'mi');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			// $queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIdsForAdmin()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		return $queryBuilder;
	}
	
	/**
	 *
	 * @return QueryBuilder
	 */
	public function filterGeneral() {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->leftJoin('a.signalisation', 'sign')->leftJoin('sign.source', 'src')->innerJoin('a.porteur', 'port')->leftJoin('a.priorite', 'priori')->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi')->select('count(a.id) total ,a.etatCourant action_etat')->groupBy('a.etatCourant');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$structure_id = $this->_user->getStructure()->getRoot();
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure($this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id))));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ANIMATEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_CHEF_PROJET)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)',  $this->checkEmptyArray($this->_user->getInstanceIdsForChefProjet())));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_MANAGER)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.porteur)', $this->_user->getCollaboratorsId()));
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
	 * @return QueryBuilder
	 */
	public function filter() {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->leftJoin('insta.chantier', 'chant')
			->leftJoin('chant.projet', 'proj')
			->leftJoin('a.signalisation', 'sign')
			->leftJoin('sign.source', 'src')
			->innerJoin('a.porteur', 'port')
			->leftJoin('a.priorite', 'priori')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
			->innerJoin('a.porteur', 'mp')
			//->leftJoin('a.contributeur', 'contrib')
			->innerJoin('a.instance', 'mi');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getStructure()->getBuPrincipal()->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ANIMATEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_CHEF_PROJET)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->checkEmptyArray($this->_user->getInstanceIdsForChefProjet())));
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
		/*if($this->_user->hasRole(Utilisateur::ROLE_CONTRIBUTEUR)) {
			$queryBuilder->orWhere('IDENTITY(contrib.utilisateur) = :userId');
		}*/
		return $queryBuilder->setParameter('userId', $this->_user->getId());
	}
	/**
	 *
	 * @return QueryBuilder
	 */
	public function filterAction() {
		$queryBuilder = $this->createQueryBuilder('a')->innerJoin('a.instance', 'insta')->leftJoin('insta.espace', 'espa')->leftJoin('a.signalisation', 'sign')->leftJoin('sign.source', 'src')->innerJoin('a.porteur', 'port')->leftJoin('a.priorite', 'priori')->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi');
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function myFilter() {
		$queryBuilder = $this->createQueryBuilder('a')->innerJoin('a.instance', 'insta')->leftJoin('insta.espace', 'espa')->leftJoin('a.signalisation', 'sign')->leftJoin('sign.source', 'src')->innerJoin('a.porteur', 'port')->leftJoin('a.priorite', 'priori')->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi')->where('port.id = :userId');
		return $queryBuilder->setParameter('userId', $this->_user->getId());
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function myFilterExport() {
		return $this->myFilter()->select('partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
				partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial, dateFinPrevue, dateCloture},
				partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
				partial cuser.{id, prenom, nom},partial port.{id, prenom, nom, structure},
				partial av.{id, description}, partial struct.{id, service, departement, pole, direction},
				GROUP_CONCAT(distinct av.description separator \' .__ \') avancements,
				GROUP_CONCAT(distinct CONCAT(cuser.prenom, \'  \', cuser.nom) ) contributeurs')
				->leftJoin('a.contributeur', 'cont')
				->leftJoin('insta.espace', 'esp')
				->leftJoin('cont.utilisateur', 'cuser')
				->leftJoin('a.avancement', 'av')
				->leftJoin('a.structure', 'struct')
				->innerJoin('a.typeAction', 'type')
				->leftJoin('a.domaine', 'dom')
				->groupBy('a.id');
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function filterExport() {
		return $this->filter()->select('partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
				partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial, dateFinPrevue, dateCloture},
				partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
				partial cuser.{id, prenom, nom},partial port.{id, prenom, nom, structure}, 
				partial av.{id, description}, partial struct.{id, service, departement, pole, direction},
				GROUP_CONCAT(distinct av.description separator \' .__ \') avancements, 
				GROUP_CONCAT(distinct CONCAT(cuser.prenom, \'  \', cuser.nom) ) contributeurs')
			->leftJoin('a.contributeur', 'cont')
			->leftJoin('insta.espace', 'esp')
			->leftJoin('cont.utilisateur', 'cuser')
			->leftJoin('a.avancement', 'av')
			->leftJoin('a.structure', 'struct')
			->innerJoin('a.typeAction', 'type')
			->leftJoin('a.domaine', 'dom')
			->groupBy('a.id');
	}
	
	public function filterExportReporting($idActions) {
		return $this->filterAction()->select('partial av.{id, description}, partial struct.{id, service, departement, pole, direction},
				partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial, dateFinPrevue, dateCloture},
				partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
				partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
				partial cuser.{id, prenom, nom},partial port.{id, prenom, nom, structure}')
			->leftJoin('a.contributeur', 'cont')
			->leftJoin('insta.espace', 'esp')
			->leftJoin('cont.utilisateur', 'cuser')
			->leftJoin('a.avancement', 'av')
			->leftJoin('a.structure', 'struct')
			->innerJoin('a.typeAction', 'type')
			->leftJoin('a.domaine', 'dom')
			->groupBy('a.id')
			->andWhere('a.id IN(:ids)')
			->setParameter('ids', $idActions)
			->getQuery()->getResult();
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a1')->select('a1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a2')->select('DISTINCT(IDENTITY(a2.instance))')->innerJoin('a2.instance', 'i2')->innerJoin('i2.bu', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	public function adminQueryBuilder2(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a2')->innerJoin('a2.structure', 's2')->innerJoin('a2.instance', 'i2')->innerJoin('i2.bu', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a3')->select('DISTINCT(i3.id)')->innerJoin('a3.porteur', 'u3')->innerJoin('a3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a4')->select('DISTINCT(IDENTITY(a4.porteur))')->innerJoin('a4.structure', 's4');
		$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a5')->select('DISTINCT(IDENTITY(so5.utilisateur))')->innerJoin('a5.signalisation', 's5')->innerJoin('s5.source', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a6')->select('DISTINCT(i6.id)')->innerJoin('a6.porteur', 'u6')->innerJoin('a6.instance', 'i6')->innerJoin('i6.chantier', 'c6')->innerJoin('c6.projet', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a7')->select('DISTINCT(IDENTITY(a7.porteur))')->innerJoin('a7.porteur', 'u7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'u7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder2(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a7')->innerJoin('a7.porteur', 'u7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'u7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	/**
	 *
	 * @return QueryBuilder
	 */
	public function rapporteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a8')->select('DISTINCT(s8.id)')->innerJoin('a8.structure', 's8');
		$data = array_merge($this->filterByProfile($queryBuilder, 's8', Utilisateur::ROLE_RAPPORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	public function getNextId() {
		$data = $this->createQueryBuilder('a')->select('MAX(a.id) as maxi')->getQuery()->getArrayResult();
		return(int) $data [0] ['maxi'] + 1;
	}
	
	/**
	 * Liste des actions dans lesquelles l\'utilisateur est
	 * porteur
	 */
	public function userActionPorteur($user_id) {
		return $this->createQueryBuilder('q')->innerJoin('q.porteur', 'p')->where('p.id = :user_id')->setParameter('user_id', $user_id)->getQuery()->getResult();
	}
	
	/**
	 * Liste des actions dans lesquelles un utilisateur est impliqué mais
	 * dans un groupe
	 */
	public function userActionGroup($user_id) {
		return $this->createQueryBuilder('q')->innerJoin('q.groupe', 'g')->innerJoin('g.membreGroupe', 'm')->innerJoin('m.utilisateur', 'u')->where('u.id = :user_id')->setParameter('user_id', $user_id)->getQuery()->getResult();
	}
	
	/**
	 * Liste des actions dans lesquelles un utilisateur est impliqué mais
	 * en tant que contributeur
	 */
	public function userActionContributeur($user_id) {
		return $this->createQueryBuilder('q')->innerJoin('q.contributeur', 'c')->innerJoin('c.utilisateur', 'u')->where('u.id = :user_id')->setParameter('user_id', $user_id)->getQuery()->getResult();
	}
	
	/**
	 * @param unknown $structure_id        	
	 */
	public function getActionByStructure($structure_id) {
		$queryBuilder = $this->filter();
		return $queryBuilder->where('IDENTITY(a.structure)=:structure_id')->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")->setParameter('structure_id', $structure_id)->getQuery()->getResult();
	}
	
	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param array $criteria
	 */
	private function filterByCriteria($queryBuilder, $criteria) {
		
	}
	
	/**
	 * Les actions des collaborateurs d'une structure
	 * @param number $structure_id        	
	 */
	public function getActionCollaborateursForExport($criteria) {
		$structure = $this->_user->getStructure();
		$queryBuilder = $this->filterExport();
		$queryBuilder->innerJoin('a.porteur', 'u')->innerJoin('a.structure', 's')
			->andWhere('u!=:userid')->setParameter('userid', $this->_user)
			->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
			->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
			->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
			->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
		$this->addCriteria($queryBuilder, $criteria, array('structure'));
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Les actions des collaborateurs d'une structure
	 * 
	 * @param unknown $structure_id        	
	 */
	public function getActionCollaborateurs($criteria) {
		$structure = $this->_user->getStructure();
		$queryBuilder = $this->filter();
		$queryBuilder->innerJoin('a.porteur', 'u')->innerJoin('a.structure', 's')
			->andWhere('u!=:userid')->setParameter('userid', $this->_user)
			->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
			->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
			->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
			->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
		$this->addCriteria($queryBuilder, $criteria, array('structure'));
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByStructForExport($structure_id, $criteria) {
		$structure = $this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
		$queryBuilder = $this->filterExport();
		if($structure) {
			$queryBuilder->leftJoin('a.structure', 's')->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
		}
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByStruct($structure_id, $criteria) {
		$structure = $this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
		$queryBuilder = $this->filter();
		$struct = $criteria ? $criteria->getStructure() : null;
		if(!isset($struct)) {
			$queryBuilder->leftJoin('a.structure', 's')->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
		}
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByInstance($instance_id, $criteria) {
		$instance = $criteria ? $criteria->getInstance() : null;
		$queryBuilder = $this->filter()->innerJoin('a.instance', 'i');
		if(!$instance) {
			$queryBuilder->andWhere('i.id=:instance_id')->setParameter('instance_id', $instance_id);
		}
		$this->addCriteria($queryBuilder, $criteria);
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByInstanceForExport($instance_id, $criteria) {
		$instance = $this->_em->getRepository('OrangeMainBundle:Instance')->find($instance_id);
		$queryBuilder = $this->filterExport();
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		$this->addCriteria($queryBuilder, $criteria, array('instance'));
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	/**
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByCodeStatut($code_statut, $criteria) {
		$queryBuilder = $this->filter()->andWhere('a.etatCourant=:code')->setParameter('code', $code_statut);
		if($criteria) {
			$this->filtres($queryBuilder, $criteria, 'a');
		}
		return $queryBuilder;
	}
	/**
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getActionByCodeStatutForExport($code_statut, $criteria) {
		$queryBuilder = $this->filterExport()->andWhere('a.etatCourant=:code')->setParameter('code', $code_statut);
		if(isset($criteria)) 
			$this->filtres($queryBuilder, $criteria, 'a');
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 *
	 * @param unknown $user        	
	 */
	public function getStatsByInstance2($role, $criteria) {
		$queryBuilder = null;
		$data = array();
		if($role == Utilisateur::ROLE_ADMIN) {
			$queryBuilder = $this->adminQueryBuilder($data);
		} elseif($role == Utilisateur::ROLE_ANIMATEUR) {
			$queryBuilder = $this->animateurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_MANAGER) {
			$queryBuilder = $this->managerQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_PORTEUR) {
			$queryBuilder = $this->porteurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_RAPPORTEUR) {
			$queryBuilder = $this->rapporteurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_CONTRIBUTEUR) {
			$queryBuilder = $this->createQueryBuilder('a')->innerJoin('a.contributeur', 'c');
			$queryBuilder = $this->filterByProfile($queryBuilder, 'c', Utilisateur::ROLE_CONTRIBUTEUR);
		} else {
			$queryBuilder = $this->filter();
		}
		$alias = $queryBuilder->getRootAlias();
		$queryBuilder->select($alias.'.id')
			->leftJoin($alias.'.actionCyclique', 'acl1')
			->leftJoin('acl1.tache', 't1')
			->leftJoin($alias.'.instance', 'i')
			->innerJoin($alias.'.porteur', 'u')
			->leftJoin('u.structure', 's')
			->andWhere($alias.".etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE' AND ".$alias.".etatCourant NOT LIKE 'SOLDEE_ARCHIVEE' AND acl1 IS NULL");
		if($role === Utilisateur::ROLE_MANAGER)
			$queryBuilder->andWhere('u!=:me')->setParameter('me', $this->_user);
		$this->filtres($queryBuilder, $criteria, $alias);
		return $queryBuilder;
	}
	/**
	 * Recuperer les stats des structures en parametre groupés statut
	 */
	public function getStatsByStructure2($role, $criteria) {
		$criteria =($criteria) ? $criteria : new \Orange\MainBundle\Entity\Action();
		$structures = null;
		$instances = null;
		$data = array();
		$user = $this->_user;
		$rep = $this->_em->getRepository('OrangeMainBundle:Structure');
		if($role === Utilisateur::ROLE_ADMIN)
			$structures = $rep->adminQueryBuilder($data)->addSelect('s2.libelle')->getQuery()->getArrayResult();
		elseif($role === Utilisateur::ROLE_ANIMATEUR) {
			$instances = $this->_em->getRepository('OrangeMainBundle:Instance')->getInstanceByRole(Utilisateur::ROLE_ANIMATEUR)->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_RAPPORTEUR)
			$structures = $rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
		elseif($role === Utilisateur::ROLE_MANAGER) {
			$structures = $rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
		} else {
			$queryBuilder = $this->filter();
		}
		$queryBuilder = $rep->createQueryBuilder('s')->select('a.id')
			->leftJoin('OrangeMainBundle:Action ', 'a', 'WITH', '1=1')
			->leftJoin('a.actionCyclique', 'acl')
			->leftJoin('acl.tache', 't')
			->innerJoin('a.porteur', 'u')
			->innerJoin('a.instance', 'i')
			->innerJoin('a.structure', 's1')
			->andWhere('s1.lvl >= s.lvl')->andWhere('s1.root = s.root')->andWhere('s1.lft  >= s.lft')->andWhere('s1.rgt <= s.rgt')
			->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE' AND a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE' AND acl IS NULL");
		if($role == Utilisateur::ROLE_ANIMATEUR) {
			$instancesIds = array();
			foreach($instances as $data)
				$instancesIds [] = \is_object($data) ? $data->getId() : $data ['id'];
			$queryBuilder->andWhere('i.id in(:insts)')->setParameter('insts', $instancesIds);
		} else {
			$structureIds = array();
			foreach($structures as $data)
				$structureIds [] = \is_object($data) ? $data->getId() : $data ['id'];
			$queryBuilder->andWhere('s.id in(:structs)')->setParameter('structs', $structureIds);
		}
		
		if($role == Utilisateur::ROLE_MANAGER)
			$queryBuilder->andWhere('u != :me')->setParameter('me', $this->_user);
		$this->filtres($queryBuilder, $criteria, 'a');
		return $queryBuilder->groupBy('a.id');
	}
	
	public function userActionContributionByInstance($instance_id) {
		$datas = $this->createQueryBuilder('a')->innerJoin('a.contributeur', 'c')->innerJoin('c.utilisateur', 'u')->where('IDENTITY(a.instance) = :intance_id')->setParameter('intance_id', $instance_id);
		// $this->valider($datas,'a');
		return $datas;
	}
	
	/**
	 * gerer les filtres
	 */
	public function filtres($queryBuilder, $criteria, $alias) {
		$criteria =($criteria) ? $criteria : new \Orange\MainBundle\Entity\Action();
		if($criteria->getPorteur()) {
			$queryBuilder->andWhere($alias.'.porteur = :porteur')->setParameter('porteur', $criteria->getPorteur());
		}
		if(count($criteria->instances) > 0) {
			$instIDs = array();
			foreach($criteria->instances as $val) {
				$instIDs [] = $val->getId();
			}
			$queryBuilder->andWhere($alias.'.instance in(:instanceIds)')->setParameter('instanceIds', $instIDs);
		}
		if($criteria->getDomaine()) {
			$queryBuilder->andWhere($alias.'.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
		}
		if($criteria->getTypeAction()) {
			$queryBuilder->andWhere($alias.'.typeAction = :type_action')->setParameter('type_action', $criteria->getTypeAction());
		}
		if($criteria->getStructure()) {
			$structure = $criteria->getStructure();
			$queryBuilder->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')->andWhere('s.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
		}
		if($criteria->statut) {
			$queryBuilder->andWhere($alias.'.etatCourant = :code')->setParameter('code', $criteria->statut);
		}
		if($criteria->hasFromDebut()) {
			$queryBuilder->andWhere($alias.'.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $criteria->hasToDebut())->setParameter('from', $criteria->hasFromDebut());
		}
		if($criteria->hasFromInitial()) {
			$queryBuilder->andWhere($alias.'.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $criteria->hasToInitial())->setParameter('from', $criteria->hasFromInitial());
		}
		if($criteria->hasFromCloture()) {
			$queryBuilder->andWhere($alias.'.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $criteria->hasToCloture())->setParameter('from', $criteria->hasFromCloture());
		}
		$queryBuilder->leftJoin($alias.'.actionGeneriqueHasAction', 'gha');
		if(count($criteria->actionsGeneriques) > 0) {
			$agIDs = array();
			foreach($criteria->actionsGeneriques as $val) {
				$agIDs [] = $val->getId();
			}
			$queryBuilder->andWhere('IDENTITY(gha.actionGenerique) IN (:agIds)')->setParameter('agIds', $agIDs);
		}
		if($criteria->hasActionGenerique) {
			$queryBuilder->andWhere('gha.id is not null');
		}
	}
	
	/**
	 * Filter les actions validees
	 */
	public function actionEchue() {
		return $this->createQueryBuilder('q')
			->where("q.etatCourant = 'ACTION_NON_ECHUE'")
			->andWhere('q.dateFinPrevue < :now')->setParameter('now', date('Y-m-d'))
			->getQuery()->getResult();
	}
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function actionNonEchue() {
		return $this->createQueryBuilder('q')
			->where("q.etatCourant = 'ACTION_ECHUE_NON_SOLDEE'")
			->andWhere('q.dateFinPrevue > :now')->setParameter('now', date('Y-m-d'))
			->getQuery()->getResult();
	}
	
	/**
	 * @param number $espace_id
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getStatsByEspace($espace_id) {
		$queryBuilder = $this->createQueryBuilder('a')->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
			->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
			->innerJoin('a.instance', 'i')->innerJoin('i.espace', 'e')
			->where('e.id =:id')->setParameter('id', $espace_id)
			->andWhere('a.etatCourant = a.etatCourant')
			->andWhere('st.display=:display')->setParameter('display', 1);
		return $queryBuilder->groupBy('a.etatCourant');
	}

	/**
	 * @param number $projet_id
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNumberByProjet($projetId) {
		$data = $this->filter()->select('COUNT(a) as number')
			->andWhere('proj.id = :projetId')->setParameter('projetId', $projetId)
			->getQuery()->getOneOrNullResult();
		return $data['number'];
	}

	/**
	 * @param number $chantierId
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getNumberByChantier($chantierId) {
		$data = $this->filter()->select('COUNT(a) as number')
			->andWhere('chant.id = :chantierId')->setParameter('chantierId', $chantierId)
			->getQuery()->getOneOrNullResult();
		return $data['number'];
	}
	
	/**
	 * @param number $projet_id
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getStatsByProjet($projet_id) {
		$queryBuilder = $this->createQueryBuilder('a')->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
			->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
			->innerJoin('a.instance', 'i')
			->innerJoin('i.chantier', 'chant')
			->where('IDENTITY(chant.projet) =:projetId')->setParameter('projetId', $projet_id)
			->andWhere('a.etatCourant = a.etatCourant')
			->andWhere('st.display=:display')->setParameter('display', 1);
		return $queryBuilder->groupBy('a.etatCourant');
	}
	
	/**
	 * @param number $chantier_id
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getStatsByChantier($chantier_id) {
		$queryBuilder = $this->createQueryBuilder('a')->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
			->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
			->innerJoin('a.instance', 'i')
			->innerJoin('i.chantier', 'chant')
			->where('chant.id =:chantierId')->setParameter('chantierId', $chantier_id)
			->andWhere('a.etatCourant = a.etatCourant')
			->andWhere('st.display=:display')->setParameter('display', 1);
		return $queryBuilder->groupBy('a.etatCourant');
	}
	
	/**
	 * Recuperer les stats generales des instances en params
	 * @param string $role
	 */
	public function getStatsGeneralByRole($role) {
		$data = array();
		if($role === Utilisateur::ROLE_ADMIN) {
			$queryBuilder = $this->adminQueryBuilder($data);
		} elseif($role == Utilisateur::ROLE_ANIMATEUR) {
			$queryBuilder = $this->animateurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_MANAGER) {
			$queryBuilder = $this->managerQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_PORTEUR) {
			$queryBuilder = $this->porteurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_RAPPORTEUR) {
			$queryBuilder = $this->rapporteurQueryBuilder($data);
		} elseif($role === Utilisateur::ROLE_CONTRIBUTEUR) {
			$queryBuilder = $this->createQueryBuilder('a')->innerJoin('a.contributeur', 'c');
			$queryBuilder = $this->filterByProfile($queryBuilder, 'c', Utilisateur::ROLE_CONTRIBUTEUR);
		}
		$alias = $queryBuilder->getRootAlias();
		$queryBuilder->select('count('.$alias.'.id) total ,'.$alias.'.etatCourant action_etat, t1.etatCourant tache_etat')
			->leftJoin($alias.'.actionCyclique', 'acl1')
			->leftJoin('acl1.tache', 't1', 'WITH', 'acl1=NULL')
			->leftJoin($alias.'.instance', 'i')
			->groupBy($alias.'.etatCourant')
			->addGroupBy('t1.etatCourant');
		$data = $queryBuilder->getQuery()->getArrayResult();
		$data = $this->combineTacheAndAction($data);
		return $data;
	}
	
	public function combineTacheAndAction($data) {
		$arrData = array();
		$i = 0;
		if(count($data) > 0)
			foreach($data as $value) {
				if(count($arrData) <= 0) {
					$arrData [$i] = array(
							'total' => intval($value ['total']) 
					);
					if($value ['action_etat'] != null)
						$arrData [$i] ['etatCourant'] = $value ['action_etat'];
				} else {
					$aide = false;
					for($j = 0; $j < count($arrData); $j ++) {
						if($value ['action_etat'] != null) {
							if($arrData [$j] ['etatCourant'] == $value ['action_etat']) {
								$arrData [$j] ['total'] += intval($value ['total']);
								$aide = true;
								break;
							}
						}
					}
					if($aide == false) {
						$i ++;
						$arrData [$i] = array(
								'total' => intval($value ['total']) 
						);
						if($value ['action_etat'] != null)
							$arrData [$i] ['etatCourant'] = $value ['action_etat'];
					}
				}
			}
		return $arrData;
	}
}

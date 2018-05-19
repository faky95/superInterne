<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\ActionCyclique;

class TacheRepository extends BaseRepository {
	
	/**
	 * @return QueryBuilder
	 */
	public function filter($criteria=null) {
		$queryBuilder = $this->createQueryBuilder('t')
			->innerJoin('t.actionCyclique', 'ac')
			->leftJoin('ac.pas', 'ps')
			->leftJoin('ac.action', 'a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->leftJoin('a.signalisation', 'sign')
			->leftJoin('sign.source', 'src')
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
		if($this->_user->hasRole(Utilisateur::ROLE_SOURCE)) {
			$queryBuilder->orWhere('IDENTITY(src.utilisateur) = :userId');
		}
		$queryBuilder->setParameter('userId', $this->_user->getId());
		$this->filterForm($queryBuilder, $criteria);
		return $queryBuilder->groupBy('t.id');
	}
	
	/**
	 *
	 * @param QueryBuilder $queryBuilder
	 * @param ActionCyclique $criteria
	 */
	public function filterForm($queryBuilder,$criteria) {
		$structure = $criteria && $criteria->getAction()? $criteria->getAction()->getStructure() : null;
		$domaine = $criteria && $criteria->getAction()? $criteria->getAction()->getDomaine() : null;
		$instance = $criteria && $criteria->getAction() ? $criteria->getAction()->getInstance() : null;
		$priorite = $criteria && $criteria->getAction()? $criteria->getAction()->getPriorite() : null;
		$type = $criteria && $criteria->getAction()? $criteria->getAction()->getTypeAction() : null;
		$porteur = $criteria && $criteria->getAction()? $criteria->getAction()->getPorteur() : null;
		$toDeb = $criteria && $criteria->getAction()? $criteria->getAction()->hasToDebut() : null;
		$fromDeb = $criteria && $criteria->getAction()? $criteria->getAction()->hasFromDebut() : null;
		$toInit = $criteria && $criteria->getAction()? $criteria->getAction()->hasToInitial() : null;
		$fromInit = $criteria && $criteria->getAction()? $criteria->getAction()->hasFromInitial() : null;
		$toClot = $criteria && $criteria->getAction()? $criteria->getAction()->hasToCloture() : null;
		$statut = $criteria && $criteria->getAction()? $criteria->getAction()->hasStatut() : null;
		$fromClot = $criteria && $criteria->getAction()? $criteria->getAction()->hasFromCloture() : null;
		if($criteria){
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
				$queryBuilder->innerJoin('a.structure', 's')->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')->andWhere('s.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
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
	}
	
	public function filterForExport($criteria) {
		$qb = $this->filter($criteria)
			->select("partial ac.{id}, partial ps.{id, libelle}, partial domth.{id, libelle}, partial dow.{id, libelle},
	             partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle}, partial a.{id, libelle, reference, description}, 
	             partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
	             partial port.{id, prenom, nom, structure}, partial struct.{id, service, departement, pole, direction}, 
				 partial t.{ id, reference, dateDebut, dateFinExecut, dateInitial, dateCloture, etatCourant }");
		return $qb->leftJoin('ac.dayOfMonth', 'domth')->leftJoin('ac.dayOfWeek', 'dow');
	}
	
	/**
	 * Filter les actions validees
	 */
	public function tacheEchue() {
		return $this->createQueryBuilder('q')
			->where("q.etatCourant = 'ACTION_NON_ECHUE'")
			->andWhere('q.dateInitial < :now')->setParameter('now', date('Y-m-d'))
			->getQuery()->getResult();
	}

	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		
	}

	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		
	}

	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		
	}

	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
 	
	}

	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		
	}

	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		
	}

	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {

	}

    
    /* ------------------------Stats ------------------------  */
    /**
     * @param unknown $user
     */
    public function getStatPorteeByInstance() {
    	$queryBuilder = $this->getTache()
	     	 ->select('t.etatCourant , COUNT(a.id) total, i.libelle')
	    	 ->innerJoin('a.instance', 'i');
        $this->valider($queryBuilder, 'a');
    	return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('i.id');
    }
   
    /**
     *
     * @param unknown $user
    */ 
    public function getStatsByInstance($criteria) {
    	$queryBuilder = $this->getTache()
	    	->select('t.etatCourant , COUNT(t.id) total, i.libelle')
	    	->innerJoin('a.instance', 'i');
    	if(isset($criteria)) {
    		$this->filtres($queryBuilder, $criteria, 'a');
    	}
	    return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('i.id');
    }
    	
    	public function getStatsByStructure($criteria) {
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder = $rep->createQueryBuilder('s')
	    		->select('t.etatCourant , COUNT(t.id) total, s.libelle')
	    		->add('from', 'OrangeMainBundle:Tache t', true)
	    		->innerJoin('t.actionCyclique', 'ac')
	    		->innerJoin('ac.action', 'a')
	    		->innerJoin('a.porteur','u')
	    		->innerJoin('u.structure','s1')
	    		->andWhere('s1.lvl >= s.lvl')
	    		->andWhere('s1.root = s.root')
	    		->andWhere('s1.lft  >= s.lft')
	    		->andWhere('s1.rgt <= s.rgt')
	    		->andWhere('s1.buPrincipal=s.buPrincipal');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('s.id');
    	}
    
    	public function getStatsByStructureInstance($criteria) {
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder = $rep->createQueryBuilder('s')
	    		->select('t.etatCourant , COUNT(t.id) total, s.libelle, i.libelle inst')
	    		->add('from', 'OrangeMainBundle:Tache t', true)
				->innerJoin('t.actionCyclique', 'ac')
				->innerJoin('ac.action', 'a')
	    		->innerJoin('a.porteur','u')
	    		->innerJoin('u.structure','s1')
	    		->innerJoin('a.instance', 'i')
	    		->andWhere('s1.lvl >= s.lvl')
	    		->andWhere('s1.root = s.root')
	    		->andWhere('s1.lft  >= s.lft')
	    		->andWhere('s1.rgt <= s.rgt')
	    		->andWhere('s1.buPrincipal=s.buPrincipal');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('s.id')->addGroupBy('i.id');
    	}

    	public function getTotalByStructure($criteria) {
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder = $rep->createQueryBuilder('s')
    				->select('COUNT(t.id) total, s.libelle')
		    		->add('from', 'OrangeMainBundle:Tache t', true)
					->innerJoin('t.actionCyclique', 'ac')
					->innerJoin('ac.action', 'a')
		    		->innerJoin('a.porteur','u')
		    		->innerJoin('u.structure','s1')
		    		->andWhere('s1.lvl >= s.lvl')
		    		->andWhere('s1.root = s.root')
		    		->andWhere('s1.lft  >= s.lft')
		    		->andWhere('s1.rgt <= s.rgt')
		    		->andWhere('s1.buPrincipal=s.buPrincipal');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		return $queryBuilder->groupBy('s.id');
    	}
    	
    	public function getStatsContribuesByInstance($criteria) {
    		$queryBuilder = $this->getTache()
	    		->select('t.etatCourant , COUNT(t.id) total, i.id, i.libelle')
	    		->innerJoin('a.contributeur', 'c')
	    		->innerJoin('c.utilisateur','u')
	    		->innerJoin('a.instance', 'i');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->groupBy('a.etatCourant')->addGroupBy('i.id');
    	}
    	
    	public function getTotalByInstance($criteria){
    		$queryBuilder =$this ->getTache()
	    		->select('COUNT(t.id) total, i.libelle')
	    		->innerJoin('a.instance','i');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		return $queryBuilder->groupBy('i.id');
    	}
    	
    	public function getTotalPorteeByInstance($user){
    		$queryBuilder =$this ->getTache()
	    		->select('COUNT(t.id) total, i.libelle')
	    		->innerJoin('a.instance','i')
	    		->innerJoin('a.porteur', 'u')
	    		->where('u=:porteur')
	    		->setParameter('porteur', $user);
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->groupBy('i.id');
    	}
    	
    	public function getTotalPorteeByStatut($user){
    		$queryBuilder = $this->getTache()
	    		->select('COUNT(distinct(t.id)) total, t.etatCourant libelle')
	    		->innerJoin('a.porteur', 'u')
	    		->where('u=:porteur')
	    		->setParameter('porteur', $user);
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->groupBy('libelle');
    	}
    	
    	public function getStatsByOneStructureInstance($structure,$criteria) {
    		$queryBuilder =$this->getTache()
	    		->select('t.etatCourant , COUNT(t.id) total, i.id, i.libelle')
	    		->innerJoin('a.porteur','u')
	    		->innerJoin('u.structure','s')
	    		->innerJoin('a.instance', 'i')
	    		->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
	    		->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
	    		->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
	    		->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt())
	    		->andWhere('s.buPrincipal <= :bu')->setParameter('bu', $structure->getBuPrincipal());
    		if(isset($criteria)) {
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		$this->valider($queryBuilder, 'a');
    		return  $queryBuilder->groupBy('t.etatCourant')->addGroupBy('i.id')->getQuery()->getArrayResult();
    	}
    	
    	public function getTotalStatsByOneStructureInstance($structure,$criteria){
    		$queryBuilder =$this->getTache()
	    		->select('COUNT(t.id) total, i.id, i.libelle')
	    		->innerJoin('a.porteur','u')
	    		->innerJoin('u.structure','s')
	    		->innerJoin('a.instance', 'i')
	    		->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
	    		->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
	    		->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
	    		->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt())
	    		->andWhere('s.buPrincipal <= :bu')->setParameter('bu', $structure->getBuPrincipal());
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		$this->valider($queryBuilder, 'a');
    		return  $queryBuilder->groupBy('i.id')->getQuery()->getArrayResult();
    	}
    	
    	public function getTotalPortee($user) {
    		$queryBuilder = $this->getTache()
		    		->select('COUNT(distinct(t.id)) total')
		    		->innerJoin('a.porteur', 'u')
		    		->where('u=:porteur')
		    		->setParameter('porteur', $user);
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->getQuery()->getArrayResult();
    	}
    	
    	public function getTotalContribueByInstance($user){
    		$queryBuilder =$this ->getTache()
	    		->select('COUNT(t.id) total, i.libelle')
	    		->innerJoin('a.instance','i')
	    		->innerJoin('a.contributeur', 'c')
	    		->innerJoin('c.utilisateur', 'u')
	    		->where('u=:contributeur')
	    		->setParameter('contributeur', $user);
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->groupBy('i.id');
    	}
    	
    	public function getTotalByStructureInstance($criteria){
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder =$rep ->createQueryBuilder('s')
	    		->select('COUNT(t.id) total, s.libelle, i.libelle inst')
	    		->add('from', 'OrangeMainBundle:Tache t', true)
	    		->innerJoin('t.actionCyclique', 'ac')
	    		->innerJoin('ac.action', 'a')
	    		->innerJoin('a.porteur','u')
	    		->innerJoin('u.structure','s1')
	    		->innerJoin('a.instance', 'i')
	    		->andWhere('s1.lvl >= s.lvl')
	    		->andWhere('s1.root = s.root')
	    		->andWhere('s1.lft  >= s.lft')
	    		->andWhere('s1.rgt <= s.rgt')
	    		->andWhere('s1.buPrincipal=s.buPrincipal');
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		$this->valider($queryBuilder,'a');
    		return $queryBuilder->groupBy('s.id')->addGroupBy('i.id');
    	}
    
    	
	  public  function getTache(){
	  	$queryBuilder = $this->createQueryBuilder('t')
		  	->innerJoin('t.actionCyclique', 'ac')
		  	->innerJoin('ac.action', 'a');
	  	return $queryBuilder;
	  }
	    
	  /**
	   * Recuperer les stats general d'un user
	   * @param unknown $user
	   */
	  public function getStatsGeneralUser($user){
	  	$queryBuilder = $this->getTache()
		  	->select('t.etatCourant , COUNT(a.id) total')
		  	->innerJoin('a.instance', 'i')
		  	->andWhere('a.porteur=:user')->setParameter('user', $user);
	  	$this->valider($queryBuilder, 'a');
	  	return $queryBuilder->groupBy('t.etatCourant');
	  }
	  
	  /**
	   * gerer les filtres
	   *
	   */
	  public function filtres($queryBuilder, $criteria,$alias){
	  	$domaine = $criteria->getDomaine();
	  	$instance = $criteria->getInstance();
	  	$porteur = $criteria->getPorteur();
	  	$type = $criteria->getTypeAction();
	  	$dateDeb = $criteria->getDateDebut();
	  	$dateInit = $criteria->getDateInitial();
	  	$dateCloture = $criteria->getDateCloture();
	  	if(isset($domaine)) {
	  		$queryBuilder->andWhere($alias.'.domaine = :domaine')
	  		->setParameter('domaine', $domaine);
	  	}
	  	if(isset($instance)) {
	  		$queryBuilder->andWhere($alias.'.instance = :instance')
	  		->setParameter('instance', $instance);
	  	}
	  	if(isset($porteur)) {
	  		$queryBuilder->andWhere($alias.'.porteur = :porteur')
	  		->setParameter('porteur', $porteur);
	  	}
	  	if(isset($type)) {
	  		$queryBuilder->andWhere($alias.'.typeAction = :type')
	  		->setParameter('type', $type);
	  	}
	  	if(isset($dateDeb)) {
	  		$queryBuilder->andWhere($alias.'.dateDebut = :dateDeb')
	  		->setParameter('dateDeb', $dateDeb);
	  	}
	  	if(isset($dateInit)) {
	  		$queryBuilder->andWhere($alias.'.dateInitial = :dateInit')
	  		->setParameter('dateInit', $dateInit);
	  	}
	  	if(isset($dateCloture)) {
	  		$queryBuilder->andWhere($alias.'.dateCloture = :dateCloture')
	  		->setParameter('dateCloture', $dateCloture);
	  	}
	  	return $queryBuilder;
	  }
	  
    /**
     * 
     * Filter les  actions validees
     */
    
    public function valider($actions,$alias){
    	return $actions	->innerJoin($alias.'.actionStatut', 'ahs')
	    	->innerJoin('ahs.statut', 'st')
	    	->andWhere('st.code=:code')
	    	->setParameter('code', Statut::ACTION_NON_ECHUE);
    }

    public function getStatsGeneralInstance($criteria,$instancesIds){
    	$queryBuilder = $this->getTache()
	    	 ->select('t.etatCourant , COUNT(t.id) total')
	    	 ->innerJoin('a.instance', 'i')
	    	 ->andWhere('i.id in(:insts)')->setParameter('insts', $instancesIds);
    	if(isset($criteria)){
    		$this->filtres($queryBuilder, $criteria, 'a');
    	}
    	return $queryBuilder->groupBy('t.etatCourant');
    }
}


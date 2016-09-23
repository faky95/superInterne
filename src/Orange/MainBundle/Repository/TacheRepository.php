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

class TacheRepository extends BaseRepository{
	

	
	/**
	 * @return QueryBuilder
	 */
	public function filter() {
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
     * 
     * @param unknown $user
     */
    public function getStatPorteeByInstance(){
    	$queryBuilder = $this->getTache()
					     	 ->select('t.etatCourant , COUNT(a.id) total, i.libelle')
					    	 ->innerJoin('a.instance', 'i')
    	;
        $this->valider($queryBuilder, 'a');
    	return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('i.id');
    }
   
	    /**
	     *
	     * @param unknown $user
	    */ 
	    public function getStatsByInstance($criteria){
    	$queryBuilder = $this->getTache()
					    	->select('t.etatCourant , COUNT(t.id) total, i.libelle')
					    	->innerJoin('a.instance', 'i')
					    	;
	    	if(isset($criteria)){
	    		$this->filtres($queryBuilder, $criteria, 'a');
	    	}
	    	return $queryBuilder->groupBy('t.etatCourant')->addGroupBy('i.id');
    	}
    	
    	public function getStatsByStructure($criteria){
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder =$rep->createQueryBuilder('s')
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
    
    	public function getStatsByStructureInstance($criteria){
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder =$rep ->createQueryBuilder('s')
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
    	public function getTotalByStructure($criteria){
    	
    		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    		$queryBuilder =$rep ->createQueryBuilder('s')
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
    	
    	public function getStatsContribuesByInstance($criteria){
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
    		->innerJoin('a.instance','i')
    		;
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
    		$queryBuilder =$this->getTache()
					    		->select('COUNT(distinct(t.id)) total, t.etatCourant libelle')
					    		->innerJoin('a.porteur', 'u')
					    		->where('u=:porteur')
					    		->setParameter('porteur', $user);
    		$this->valider($queryBuilder, 'a');
    		return $queryBuilder->groupBy('libelle');
    	}
    	
    	public function getStatsByOneStructureInstance($structure,$criteria){
    		$queryBuilder =$this->getTache()
					    		->select('t.etatCourant , COUNT(t.id) total, i.id, i.libelle')
					    		->innerJoin('a.porteur','u')
					    		->innerJoin('u.structure','s')
					    		->innerJoin('a.instance', 'i')
					    		->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
					    		->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
					    		->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
					    		->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt())
					    		->andWhere('s.buPrincipal <= :bu')->setParameter('bu', $structure->getBuPrincipal())
					    		;
    	
    		if(isset($criteria)){
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
    		->andWhere('s.buPrincipal <= :bu')->setParameter('bu', $structure->getBuPrincipal())
    		;
    		 
    		if(isset($criteria)){
    			$this->filtres($queryBuilder, $criteria, 'a');
    		}
    		$this->valider($queryBuilder, 'a');
    		return  $queryBuilder->groupBy('i.id')->getQuery()->getArrayResult();
    	}
    	public function getTotalPortee($user){
    		$queryBuilder =$this->getTache()
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
    		$queryBuilder->groupBy('s.id')->addGroupBy('i.id');
    	
    		return $queryBuilder;
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
	  	->andWhere('a.porteur=:user')->setParameter('user', $user)
	  	;
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
    	->setParameter('code', Statut::ACTION_NON_ECHUE)
    	;
    }

    public function getStatsGeneralInstance($criteria,$instancesIds){
    	$queryBuilder = $this->getTache()
					    	 ->select('t.etatCourant , COUNT(t.id) total')
					    	 ->innerJoin('a.instance', 'i')
					    	 ->andWhere('i.id in(:insts)')->setParameter('insts', $instancesIds)
					    	 ;
    	if(isset($criteria)){
    		$this->filtres($queryBuilder, $criteria, 'a');
    	}
    	return $queryBuilder->groupBy('t.etatCourant');
    }
}


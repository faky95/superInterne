<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class TypeActionRepository extends BaseRepository {

	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}
	
	public  function listQueryBuilder(){
		return $this->filter();
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('t');
		$queryBuilder->where($queryBuilder->expr()->in('t.id', $this->superAdminQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->adminQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->animateurQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->chefProjetQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->managerQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->porteurQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('t.id', $this->sourceQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	}
	

	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t1')->select('t1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t2')->select('t2.id')
							  ->innerJoin('t2.bu', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t3')->select('t3.id')
		->innerJoin('t3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t4')->select('t4.id')
							 ->innerJoin('t4.bu', 'b4')
							 ->innerJoin('b4.structureBuPrincipal', 's4');
		$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t5')->select('t5.id')
		->innerJoin('t5.instance', 'i5')
		->innerJoin('i5.sourceInstance', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t6')->select('t6.id')
							 ->innerJoin('t6.projet', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('t7')->select('t7.id')
							->innerJoin('t7.action', 'a7')
							->innerJoin('a7.porteur', 'ut7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'ut7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	
	public function getArrayTypeAction(){
     $typeActions= $this->filter()->select('t.id')->getQuery()->getArrayResult();	
     $tabTypeAction=array();
     $i=0;
     foreach ($typeActions as $ta){
     	$tabTypeAction[$i]=$ta['id'];
     	$i++;
     }
     return $tabTypeAction;
	}
      
	public function listByInstance($id) {
		return $this->createQueryBuilder('t')
			->innerJoin('t.instance', 'i')
			->where('i.id = :id')
			->setParameter('id', $id)
			->getQuery()->getArrayResult();
	}
	
	public function listTypeByInstance($id) {
		return $this->createQueryBuilder('t')
		->innerJoin('t.instance', 'i')
		->where('i.id = :id')
		->setParameter('id', $id)
		->getQuery()->getArrayResult();
	}

	/**
	 * @return QueryBuilder
	 */
	public function getTypesByEspace($espace_id) {
		$queryBuilder = $this->createQueryBuilder('t')
		->innerJoin('t.instance', 'i')
		->innerJoin('i.espace', 'e')
		->where('e.id=:id')->setParameter('id', $espace_id);
		return $queryBuilder;
	}
}

?>
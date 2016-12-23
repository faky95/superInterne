<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class BuRepository extends BaseRepository{
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}
	
	
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('b');
		$queryBuilder->where($queryBuilder->expr()->in('b.id', $this->superAdminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('b.id', $this->adminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('b.id', $this->animateurQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('b.id', $this->managerQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('b1')->select('b1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('b2')->select('b2.id');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('b3')->select('b3.id')
		->innerJoin('b3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('b4')->select('b4.id')
		->innerJoin('b4.structureBuPrincipal', 's4');
		$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	
	
	
	
}

?>
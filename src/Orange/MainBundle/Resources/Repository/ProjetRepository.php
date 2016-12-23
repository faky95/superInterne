<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class ProjetRepository extends BaseRepository {

	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('b');
		$queryBuilder->where($queryBuilder->expr()->in('b.id', $this->superAdminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('b.id', $this->adminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('b.id', $this->chefProjetQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	}
		/**
		 * @return QueryBuilder
		 */
		public function superAdminQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('p1')->select('p1.id');
			$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function chefProjetQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('p2')->select('p2.id');
			$data = array_merge($this->filterByProfile($queryBuilder, 'p2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
	
	}
	
	

?>
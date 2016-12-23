<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\MainBundle\Entity\Espace;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class ChantierRepository extends BaseRepository {
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter()->getQuery ()->execute();
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('c');
		$queryBuilder->where($queryBuilder->expr()->in('c.id', $this->superAdminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('c.id', $this->chefProjetQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('c1')->select('c1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}	
	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('c6')->select('c6.id')
		->innerJoin('c6.projet', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
}
?>
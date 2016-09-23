<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\MainBundle\Entity\Espace;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class EspaceRepository extends BaseRepository {
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('e');
		$queryBuilder->where($queryBuilder->expr()->in('e.id', $this->superAdminQueryBuilder($data)->getDQL()))
					 ->orWhere($queryBuilder->expr()->in('e.id', $this->membreEspaceQueryBuilder($data)->getDQL()))
		;
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	
	}
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('e1')->select('e1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	
	/**
	 * @return QueryBuilder
	 */
	public function membreEspaceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('e3')->select('e3.id');
		$data = array_merge($this->filterByProfile($queryBuilder, 'e3', Utilisateur::ROLE_MEMBRE_ESPACE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	
}
?>
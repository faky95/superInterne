<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends BaseRepository{
	
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('n');
		$queryBuilder->where($queryBuilder->expr()->in('n.id', $this->superAdminQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('n.id', $this->adminQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('n.id', $this->managerQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		return $queryBuilder->setParameters($parameters);
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('n1')->select('n1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('n2')->select('n2.id')
			->innerJoin('n2.destinataire', 'd2')
			->innerJoin('d2.structure', 's2')
			->innerJoin('s2.buPrincipal', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('n3')->select('n3.id')
			->innerJoin('n3.destinataire', 'd3')
			->innerJoin('d3.structure', 's3');
		$data = array_merge($this->filterByProfile($queryBuilder, 's3', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
}

?>
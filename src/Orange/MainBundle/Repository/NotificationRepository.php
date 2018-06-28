<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class NotificationRepository extends BaseRepository{
	
	
	/**
	 * {@inheritDoc}
	 */
	public function listNotifQueryBuilder($criteria) {
		$criteria = $criteria ? $criteria : new \Orange\MainBundle\Entity\Notification();
		// TODO: Auto-generated method stub
		$queryBuilder = $this->filter()->select('n, t, c, d')
			->innerJoin('n.typeNotification', 't')->leftJoin('n.copy', 'c')->leftJoin('n.destinataire', 'd');
		if($criteria->getTypeNotification()) {
			$queryBuilder->andWhere('t.id = :typeNotification')
				->setParameter('typeNotification', $criteria->getTypeNotification()->getId());
		}
		$copy = $destinataire = array();
		foreach($criteria->getCopy() as $value) {
			$copy[] = $value->getId();
		}
		foreach($criteria->getDestinataire() as $value) {
			$destinataire[] = $value->getId();
		}
		if(count($copy)) {
			$queryBuilder->andWhere('c.id IN (:copy)')->setParameter('copy', $copy);
		}
		if(count($destinataire)) {
			$queryBuilder->andWhere('d.id IN (:destinataire)')->setParameter('destinataire', $destinataire);
		}
		if($criteria->startDate) {
			$queryBuilder->andWhere('date(n.date) >= :startDate')->setParameter('startDate', $criteria->startDate->format('Y-m-d'));
		}
		if($criteria->endDate) {
			$queryBuilder->andWhere('date(n.date) <= :endDate')->setParameter('endDate', $criteria->endDate->format('Y-m-d'));
		}
		return $queryBuilder;
	}
	
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
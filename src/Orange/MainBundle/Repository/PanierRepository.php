<?php 
namespace Orange\MainBundle\Repository;

class PanierRepository extends BaseRepository {
	
	
	/**
	 * @return \Orange\MainBundle\Entity\Panier
	 */
	public function findMine() {
		return $this->createQueryBuilder('q')
			->andWhere('q.utilisateur = :utilisateur')->setParameter('utilisateur', $this->_user)
			->andWhere('q.etat = :etat')->setParameter('etat', true)
			->getQuery()
			->getOneOrNullResult();
	}
}

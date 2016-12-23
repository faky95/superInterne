<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class AnimateurRepository extends BaseRepository{
	
	public function findAnimateurs($id) {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'i')
			->andWhere('i.id = :id')->setParameter('id', $id)
			->getQuery()
			->execute();
		return $queryBuilder;
	}
	
}

?>
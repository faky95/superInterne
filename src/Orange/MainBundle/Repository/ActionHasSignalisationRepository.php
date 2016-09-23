<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class ActionHasSignalisationRepository extends BaseRepository{
	
	public function findActions($id) {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.signalisation', 'sign')
			->andWhere('sign.id ='.$id);
		return $queryBuilder->getQuery()->execute();
	}
	public function findIdActions($id) {
		$queryBuilder = $this->createQueryBuilder('a')
		->select('act.id')
		->innerJoin('a.action', 'act')
		->innerJoin('a.signalisation', 'sign')
		->andWhere('sign.id ='.$id);
		return $queryBuilder->getQuery()->execute();
	}
	
}

?>
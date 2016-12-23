<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class InstanceHasTypeActionRepository extends BaseRepository{
	
	public function findtypes($id) {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'inst')
			->andWhere('inst.id ='.$id);
		return $queryBuilder->getQuery()->execute();
	}
	
}

?>
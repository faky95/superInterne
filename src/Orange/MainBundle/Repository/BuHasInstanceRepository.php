<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class BuHasInstanceRepository extends BaseRepository{
	
	public function findInstances($bu) {
		$queryBuilder = $this->createQueryBuilder('a')
			->select('a.id')
			->innerJoin('a.bu', 'bu')
			->andWhere('bu.id ='.$bu);
		$ids = array();
		foreach ($queryBuilder->getQuery()->execute() as $value){
			array_push($ids, $value['id']);
		}
		return $ids;
	}
	
}

?>
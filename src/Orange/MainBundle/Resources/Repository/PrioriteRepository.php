<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class PrioriteRepository extends BaseRepository {

	public function listAllPriorite($bu) {
		$queryBuilder= $this->createQueryBuilder('p')
		->innerJoin('p.bu', 'b')
		->where('b=:bu')->setParameter('bu', $bu);
		return $queryBuilder;
	
	}
}

?>
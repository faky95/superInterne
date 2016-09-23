<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\MainBundle\Entity\Espace;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class ContributeurRepository extends BaseRepository {
	
	public function findContributeurs($id){
		return $this->createQueryBuilder('c')
			->andWhere('c.action = :action')->setParameter('action', $id)
			->getQuery()
			->execute();
	}
		
}
?>
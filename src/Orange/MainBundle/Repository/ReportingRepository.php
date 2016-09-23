<?php
namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\CustomInterface\RepositoryInterface;
use Orange\MainBundle\Entity\Reporting;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Entity\Priorite;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Statut;

class ReportingRepository extends BaseRepository{
	
	
	
	public function listAllReporting($user) {
		$queryBuilder= $this->createQueryBuilder('r')
					->innerJoin('r.utilisateur', 'u')
					->where('u = :user')->setParameter('user', $user);
		return $queryBuilder;
		
	}
}
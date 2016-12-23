<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\CustomInterface\RepositoryInterface;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\TypeAction;
use Orange\MainBundle\Entity\Priorite;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Statut;

class EnvoiRepository extends BaseRepository{
	
	public function getEnvoi()
	{
		$date =  date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
		->andWhere('e.dateEnvoi = :date')->setParameter('date', $date)
		->getQuery()
		->execute();
		return $queryBuilder;
		
	}
	public function getEnvoiStructure($bu = null, $espace = null, $projet = null)
	{
		$date =  '2016-12-05';//date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
		    ->innerJoin('e.reporting', 'r')
		    ->innerJoin('r.utilisateur', 'u')
		    ->innerJoin('u.structure', 's')
			->where('e.typeReporting = 1')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', $date)
			;
		if($bu) {
				$queryBuilder->andWhere('s.buPrincipal = :bu')->setParameter('bu', $bu);
		}
	    return $queryBuilder->getQuery()->execute();
	}
	public function getEnvoiInstance($bu = null, $espace = null, $projet = null) {
		$date =  date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
			->innerJoin('e.reporting', 'r')
			->innerJoin('r.utilisateur', 'u')
			->innerJoin('u.structure', 's')
			->where('e.typeReporting = 2')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', $date);
		if($bu) {
			$queryBuilder->andWhere('s.buPrincipal = :bu')->setParameter('bu', $bu);
		}
		return $queryBuilder->getQuery()->execute();
	}
	
}


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
	public function getEnvoiStructure()
	{
		$date =  date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
			->where('e.typeReporting = 1')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', $date)
			->getQuery()
			->execute();
			return $queryBuilder;
	}
	public function getEnvoiInstance()
	{
		$date =  date('Y-m-d');
		$queryBuilder = $this->createQueryBuilder('e')
			->where('e.typeReporting = 2')
			->andWhere('e.dateEnvoi = :date')->setParameter('date', $date)
			->getQuery()
			->execute();
		return $queryBuilder;
	}
	
}


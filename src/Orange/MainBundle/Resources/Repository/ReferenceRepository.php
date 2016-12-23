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

class ReferenceRepository extends BaseRepository{
	
	public function getBySymbole($symbole) {
		$datas= $this->findOneBy(array('symbole'=>$symbole));
		return $datas;
	}
	public function listAll()
	{
		$queryBuilder = $this->createQueryBuilder('q')
		->getQuery()
		->getResult();
		return $queryBuilder;
	}
}
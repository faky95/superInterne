<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class PasRepository extends BaseRepository{
	
	public function listAllPas(){
		$queryBuilder = $this->createQueryBuilder('s');
		return $queryBuilder;
	}
}

?>
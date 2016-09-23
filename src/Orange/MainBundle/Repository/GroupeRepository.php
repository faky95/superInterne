<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\MainBundle\Entity\Espace;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;

class GroupeRepository extends BaseRepository {
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}
	
	public function filter() {
		$queryBuilder =  $this->createQueryBuilder ( 'g' ) ;
			return $queryBuilder;
	
	}
	
	
}
?>
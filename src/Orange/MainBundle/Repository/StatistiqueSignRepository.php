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

class StatistiqueSignRepository extends BaseRepository{
	
	public  function getStatsAnimateur($instances){
		$semaineCourante=date("W")-1;
		$anneeCourante=date("Y");
		$instanceIds=array();
		foreach ($instances as $inst){
			$instanceIds[]=$inst['id'];
		}
		$queryBuilder=$this->createQueryBuilder('st')
							->select('  i.id inst,
									    SUM(st.nbEfficace) nbEfficace,
									    SUM(st.nbNonEfficace) nbNonEfficace,
										SUM(st.nbEnCours) nbEnCours,
									    SUM(st.nbCloturee) nbCloturee,
										SUM(st.nbTotal) nbTotal	')
							->innerJoin('st.instance', 'i')
							->where('i IN (:instanceIds)')->setParameter('instanceIds', $instanceIds)
							->andWhere('st.semaine=:semaineCourante')->setParameter('semaineCourante', $semaineCourante)
							->andWhere('st.annee=:anneeCourante')->setParameter('anneeCourante', $anneeCourante)
							->groupBy('i.id')
							->orderBy('i.id')->getQuery()->getArrayResult();
		return $queryBuilder;
	}

}
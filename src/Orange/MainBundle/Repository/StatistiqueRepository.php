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

class StatistiqueRepository extends BaseRepository{
	
	
	public function getStatistiqueEvolutiveByInstance($criteria){
		$queryBuilder=$this->createQueryBuilder('stat');
		$anneeCourante=date("Y");
		$queryBuilder->select('         i.id f_id,
										i.libelle f_libelle,
										stat.semaine s_id, 
										stat.semaine  s_libelle,
									    SUM(stat.nbAbandon) nbAbandon,
										SUM(stat.nbDemandeAbandon) nbDemandeAbandon,
										SUM(stat.nbFaiteDelai) nbFaiteDelai,
										SUM(stat.nbFaiteHorsDelai) nbFaiteHorsDelai,
										SUM(stat.nbNonEchue) nbNonEchue,
										SUM(stat.nbEchueNonSoldee) nbEchueNonSoldee,
										SUM(stat.nbNonEchueNonSoldee) nbNonEchueNonSoldee,
										SUM(stat.nbSoldeeHorsDelais) nbSoldeeHorsDelais,
										SUM(stat.nbSoldeeDansLesDelais) nbSoldeeDansLesDelais,
										SUM(stat.total) total')
										->innerJoin('stat.instance', 'i')
										->innerJoin('stat.type', 't')
										->andWhere('t.id=:type')->setParameter('type', 1)
										->andWhere('stat.annee=:anneeCourante')->setParameter('anneeCourante', $anneeCourante)
										;
										$queryBuilder=$this->filtres($queryBuilder, $criteria, 'stat');
		return $queryBuilder->groupBy('f_id')->addGroupBy('s_id');
	}
	
	
	public function getStatistiqueEvolutiveByStructure($criteria){
		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
		$anneeCourante=date("Y");
		$queryBuilder=$rep->createQueryBuilder('s')
							->add('from', 'OrangeMainBundle:Statistique stat', true)
							->select(' 
										s.id f_id,
										s.libelle f_libelle,
										stat.semaine s_id,
										stat.semaine s_libelle,
										SUM(stat.nbAbandon) nbAbandon,
									    SUM(stat.nbDemandeAbandon) nbDemandeAbandon,
										SUM(stat.nbEchueNonSoldee) nbEchueNonSoldee,
									    SUM(stat.nbFaite) nbFaite,
										SUM(stat.nbFaiteHorsDelai) nbFaiteHorsDelai,
									    SUM(stat.nbNonEchue) nbNonEchue,
									    SUM(stat.nbNonEchueNonSoldee) nbNonEchueNonSoldee,
									    SUM(stat.nbSoldeeHorsDelais) nbSoldeeHorsDelais,
										SUM(stat.nbFaiteDelai) nbFaiteDelai,
									    SUM(stat.nbSoldeeDansLesDelais) nbSoldeeDansLesDelais,
										SUM(stat.total) total,
										SUM(stat.nbUtilisateur) nbUtilisateur,
										SUM(stat.nbUtilisateurActif) nbUtilisateurActif
									')
							->innerJoin('stat.structure','s1')
							->andWhere('s1.lvl >= s.lvl')
							->andWhere('s1.root = s.root')
							->andWhere('s1.lft  >= s.lft')
							->andWhere('s1.rgt <= s.rgt')							
							->andWhere('stat.type=:val')->setParameter('val', 1)
							->andWhere('stat.annee=:anneeCourante')
							->setParameter('anneeCourante', $anneeCourante)
							;
		
		$queryBuilder=$this->filtres($queryBuilder, $criteria, 'stat');
		return $queryBuilder->groupBy('f_id')
							->addGroupBy('s_id')->getQuery()->getArrayResult();
	}
	


	
	/**
	 * Recuperer les stats d'un user en fontion du type(portee , contribution)
	 * @param unknown $user
	 */
	public function getStatsUserByInstance($user, $type){
		$data=$this->createQueryBuilder('st')
					->select('				i.id id,
											i.libelle,
											SUM(st.nbAbandon) nbAbandon,
											SUM(st.nbDemandeAbandon) nbDemandeAbandon,
											SUM(st.nbFaiteDelai) nbFaiteDelai,
											SUM(st.nbFaiteHorsDelai) nbFaiteHorsDelai,
											SUM(st.nbNonEchue) nbNonEchue,
											SUM(st.nbEchueNonSoldee) nbEchueNonSoldee,
											SUM(st.nbNonEchueNonSoldee) nbNonEchueNonSoldee,
											SUM(st.nbSoldeeHorsDelais) nbSoldeeHorsDelais,
											SUM(st.nbSoldeeDansLesDelais) nbSoldeeDansLesDelais,
											SUM(st.total) total')
					->innerJoin('st.instance', 'i')
					->innerJoin('st.utilisateur', 'u')
					->innerJoin('st.type', 'type')
					->where('u=:porteur')->setParameter('porteur', $user)
					->andWhere('type.id=:val')->setParameter('val', $type)
		;
		return $data->groupBy('i.id');
	}

	/* stats*/
	public function getStatsUserBySemaine($user,$type, $criteria){
		$anneeCourante=date("Y");
		$data=$this->createQueryBuilder('st')
					->select('st.semaine id,
							st.semaine libelle,
							SUM(st.nbAbandon) nbAbandon,
							SUM(st.nbDemandeAbandon) nbDemandeAbandon,
							SUM(st.nbFaiteDelai) nbFaiteDelai,
							SUM(st.nbFaiteHorsDelai) nbFaiteHorsDelai,
							SUM(st.nbNonEchue) nbNonEchue,
							SUM(st.nbEchueNonSoldee) nbEchueNonSoldee,
							SUM(st.nbNonEchueNonSoldee) nbNonEchueNonSoldee,
							SUM(st.nbSoldeeHorsDelais) nbSoldeeHorsDelais,
							SUM(st.nbSoldeeDansLesDelais) nbSoldeeDansLesDelais,
							SUM(st.total) total')
					->innerJoin('st.type', 't')
					->where('st.utilisateur=:usr')->setParameter('usr',$user)
					->andWhere('t.id=:type')->setParameter('type', $type)
					->andWhere('st.annee=:anneeCourante')->setParameter('anneeCourante', $anneeCourante)
					->orderBy('st.semaine')
					->groupBy('libelle');
		$data=$this->filtres($data, $criteria, 'st'); 
				
		return $data->getQuery()->getArrayResult();
	}
	public function filtres($queryBuilder,$criteria, $alias){
		$criteria=($criteria)?$criteria: new \Orange\MainBundle\Entity\Statistique();
		if($criteria->getUtilisateur()){
			$queryBuilder->andWhere($alias.'.utilisateur = :utilisateur')->setParameter('utilisateur', $criteria->getUtilisateur());
		} 
		if(count($criteria->instances)>0){
			$instancesIds=array();
			foreach ($criteria->instances as $key=>$data)
				$instancesIds[]=(\is_object($data))?$data->getId():$data['id'];
				$queryBuilder->andWhere($alias.'.instance in (:instanceIds)')->setParameter('instanceIds', $instancesIds);
		}
		if($criteria->getDomaine()){
			$queryBuilder->andWhere($alias.'.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
		}
		if($criteria->getTypeAction()){
			$queryBuilder->andWhere($alias.'.typeAction = :type_action')->setParameter('type_action', $criteria->getTypeAction());
		}
		if($criteria->getStructure()){
			$structure=$criteria->getStructure();
			$aliases=$queryBuilder->getAllAliases();
			if(!\in_array('s', $aliases))
				$queryBuilder->innerJoin($alias.'.structure','s');
			$queryBuilder
			->andWhere('s.lvl >= :level')
			->andWhere('s.root = :root')
			->andWhere('s.lft >= :left')
			->andWhere('s.rgt <= :right')
			->setParameter('level', $structure->getLvl())
			->setParameter('root', $structure->getRoot())
			->setParameter('left', $structure->getLft())
			->setParameter('right', $structure->getRgt());
		}
		return $queryBuilder;
	}


	
}
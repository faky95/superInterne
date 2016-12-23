<?php 
namespace Orange\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Orange\MainBundle\Entity\Statut;

class ActionStatutRepository extends EntityRepository {
	
	
	public function getLastManagerValidation($action_id) {
		return $this->createQueryBuilder('q')
				->innerJoin('q.utilisateur', 'u')
				->innerJoin('q.action', 'a')
				->where('a.id =:action_id')
				->andWhere('u.manager =:ismanager')
				->orderBy('q.id', 'DESC')
				->setParameters(array('action_id' => $action_id, 'ismanager'=>true))
				->setMaxResults(1)
				->getQuery()
				->getOneOrNullResult();
	}
	
	public function statistiqueUtilisateur($action_liste, $all) {
		$today = new \DateTime();
		$today = $today->format('Y-m-d');
		$config = $this->getEntityManager()->getConfiguration();
		$config->addCustomDatetimeFunction('WEEK', 'DoctrineExtensions\Query\Mysql\Week');
		$firstDayYear = date('Y-1-1');
		if($all === null) {
			$from = date("Y-m-d", strtotime("-7 day", strtotime($today)));
		} else {
			$from = $firstDayYear;
		}
		$to = date("Y-m-d", strtotime("-1 day", strtotime($today)));
		return $this->createQueryBuilder('q')
					->select('s.code AS type_statut, inst.id AS instance_id, WEEK(q.dateStatut) as semaine, dom.id AS domaine_id, typ.id AS type_action_id, COUNT(a.id) as nombre')
					->leftJoin('q.statut', 's')
					->leftJoin('q.action', 'a')
					->leftJoin('a.instance', 'inst')
					->leftJoin('a.domaine', 'dom')
					->leftJoin('a.typeAction', 'typ')
					->where('a.id IN (:action_liste)')
 					->andWhere('q.dateStatut BETWEEN :from AND :to')
					->groupBy('type_statut, instance_id, domaine_id, type_action_id, semaine')
					->setParameters(array('from' => $from, 'to' => $to, 'action_liste' => $action_liste))
					->getQuery()
					->getResult();
	}
	
	public function isLastActionStatut($action) {
		return $this->createQueryBuilder('q')
					->select('s.code as code_statut')
					->innerJoin('q.statut', 's')
					->innerJoin('q.action', 'a')
					->where('a.id =:action_id')
					->orderBy('q.id', 'DESC')
					->setParameter('action_id', $action->getId())
					->setMaxResults(1)
					->getQuery()
					->getOneOrNullResult();
	}
	
	public function isActionEchue($action) {
		return $this->createQueryBuilder('q')
					->innerJoin('q.statut', 's')
					->innerJoin('q.action', 'a')
					->where('a.id =:action_id')
					->andWhere('s.code =:code_statut')
					->setParameters(array('action_id' => $action->getId(), 'code_statut' => Statut::ACTION_ECHUE_NON_SOLDEE))
					->getQuery()
					->getResult();
	}
	
}

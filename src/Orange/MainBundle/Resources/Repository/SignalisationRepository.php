<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\CustomInterface\RepositoryInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Orange\MainBundle\Entity\Statut;

class SignalisationRepository extends BaseRepository {
	
	public function findAll() {
		//TODO: Auto-generated method stub
		return $this->filter()->getQuery()->execute();
	}
	
	public function getSignalisations($ids){
		return $this->createQueryBuilder('s')
		->where('s.id IN (:ids)')
		->setParameters(array('ids' => $ids))
		->getQuery()
		->getResult();
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('sign');
		$queryBuilder->where($queryBuilder->expr()->in('sign.id', $this->superAdminQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('sign.id', $this->adminQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('sign.id', $this->animateurQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('sign.id', $this->managerQueryBuilder($data)->getDQL()))
			->orWhere($queryBuilder->expr()->in('sign.id', $this->sourceQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		return $queryBuilder->setParameters($parameters);
	}


	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('sign1')->select('sign1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('sign2')->select('sign2.id')
		                     ->innerJoin('sign2.instance', 'i2')
						   	 ->innerJoin('i2.bu', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('sign3')->select('sign3.id')
			->innerJoin('sign3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('sign4')->select('sign4.id')
							->innerJoin('sign4.instance', 'i4')
							->innerJoin('i4.animateur', 'a4')
							->andWhere('a4.utilisateur = :user')->setParameter('user', $this->_user);
		$data = array_merge($queryBuilder->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('sign5')->select('sign5.id')
							->innerJoin('sign5.instance', 'i5')
							->innerJoin('i5.sourceInstance', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	public function findByAction($action_id){
		
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('Orange\MainBundle\Entity\Signalisation', 's');
		$rsm->addFieldResult('s', 'id', 'id');
		
		$sql = 'SELECT s.id FROM project p JOIN project_related pr ON p.id = pr.related_project_id WHERE pr.project_id = ?';
		
		$query = $this->_em->createNativeQuery($sql, $rsm);
		$query->setParameter(1, $idProject);
		
		$projects = $query->getResult();
		
		
		
			return $this->createQueryBuilder('s')
			            ->innerJoin('s.action', 'a', 'WITH', 'a.id = :action_id')
			            ->setParameter('action_id', $action_id)
						->getQuery()
						->getResult();
		}
	
	public function getNextId() {
		$data = $this->createQueryBuilder('s')
		->select('MAX(s.id) as maxi')
		->getQuery()->getArrayResult();
		return (int)$data[0]['maxi'] + 1;
	}
	
	/**
	 * 
	 * @param Signalisation $criteria
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function listAllElements($criteria){
		$queryBuilder = $this->filter();
		$fromDateSignale= $criteria->fromDateSignale;
		$toDateSignale= $criteria->toDateSignale;
		$fromDateConstat= $criteria->getFromDateConstat();
		$toDateConstat= $criteria->getToDateConstat();
		$perimetre = $criteria->getPerimetre();
		$constatateur = $criteria->getConstatateur();
		$source = $criteria->getUtilisateur();
		$domaine = $criteria->getDom();
		$type = $criteria->getType();
		$statut = $criteria->getStatut();
		if($type) {
			$queryBuilder->andWhere('sign.typeSignalisation = :type')->setParameter('type', $type);
		}
		if($domaine) {
			$queryBuilder->andWhere('sign.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($statut) {
			$queryBuilder->andWhere('sign.etatCourant = :code')->setParameter('code', $statut->getCode());
		}
		if($constatateur) {
			$queryBuilder->andWhere('sign.constatateur = :const')->setParameter('const', $constatateur);
		}
		if($source) {
			$queryBuilder->innerJoin('sign.source', 'sour')->andWhere('sour.utilisateur = :source')->setParameter('source', $source);
		}
		if($perimetre) {
			$queryBuilder->andWhere('sign.instance = :perimetre')->setParameter('perimetre', $perimetre);
		}
		if($fromDateConstat) {
			$queryBuilder->andWhere('sign.dateConstat >= :from')->setParameter('from', $fromDateConstat);
		}
		if($toDateConstat){
			$queryBuilder->andWhere('sign.dateConstat <= :to')->setParameter('to', $toDateConstat);
		}
		if($fromDateSignale) {
			$queryBuilder->andWhere('sign.dateSignale >= :fromsign')->setParameter('fromsign', $fromDateSignale);
		}
		if($toDateSignale){
			$queryBuilder->andWhere('sign.dateSignale <= :tosign')->setParameter('tosign', $toDateSignale);
		}
		return $queryBuilder;
	}
	
	public function totalSignalisation()
	{
		$data= $this->filter()
					->select('COUNT(DISTINCT sign.id) as total')
					->innerJoin('sign.signStatut', 'signSta')
					->innerJoin('signSta.statut', 's')
 					->andWhere('s.code =:statut_code')
 					->setParameter('statut_code', Statut::SIGNALISATION_PRISE_CHARGE)
					->getQuery()
					->getOneOrNullResult()
					;
		return $data['total'];
	}
	
	public function forCanevas($criteria){
		$queryBuilder = $this->filter();
		$fromDateConstat= $criteria->getFromDateConstat();
		$toDateConstat= $criteria->getToDateConstat();
		$fromDateSignale= $criteria->getFromDateSignale();
		$toDateSignale= $criteria->getToDateSignale();
		$perimetre = $criteria->getPerimetre();
		$constatateur = $criteria->getConstatateur();
		$source = $criteria->getUtilisateur();
		$domaine = $criteria->getDom();
		$type = $criteria->getType();
		$statut = $criteria->getStatut();
		if($type) {
			$queryBuilder->andWhere('sign.typeSignalisation = :type')->setParameter('type', $type);
		}
		if($domaine) {
			$queryBuilder->andWhere('sign.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($statut) {
			$queryBuilder->andWhere('sign.etatCourant = :code')->setParameter('code', $statut->getCode());
		}
		if($constatateur) {
			$queryBuilder->andWhere('sign.constatateur = :const')->setParameter('const', $constatateur);
		}
		if($source) {
			$queryBuilder->innerJoin('sign.source', 'sour')->andWhere('sour.utilisateur = :source')->setParameter('source', $source);
		}
		if($perimetre) {
			$queryBuilder->andWhere('sign.instance = :perimetre')->setParameter('perimetre', $perimetre);
		}
		if($fromDateConstat) {
			$queryBuilder->andWhere('sign.dateConstat >= :from and sign.dateConstat <= :to')->setParameter('to', $toDateConstat)->setParameter('from', $fromDateConstat);
		}
		if($fromDateSignale) {
			$queryBuilder->andWhere('sign.dateSignale >= :from and sign.dateSignale <= :to')->setParameter('to', $toDateSignale)->setParameter('from', $fromDateSignale);
		}
		return $queryBuilder->andWhere("sign.etatCourant LIKE '%NOUVELLE%'");
	}
	
	public function actionSignalisationId($signalisation_id)
	{
		$connection = $this->_em->getConnection();
		$statement = $connection->prepare("SELECT action_id FROM action_has_signalisation WHERE signalisation_id = :signalisation_id");
		$statement->bindValue('signalisation_id', $signalisation_id);
		$statement->execute();
		$results = $statement->fetchAll();
		return $results;
	}
	
	public function nouvelleSignalisation()
	{
		$date =  new \DateTime();
		$queryBuilder = $this->createQueryBuilder('i')
		->innerJoin('i.source', 'u')
		->innerJoin('i.instance', 'ins')
		->innerJoin('i.constatateur', 'd')
		->where('i.dateSignale < :date')
		->andWhere("i.etatCourant LIKE 'SIGN_NOUVELLE'")
		->orderBy('i.id', 'ASC')
		->setParameters(array('date' => $date->format('Y-m-d')
	
		))->getQuery()->execute();
		return $queryBuilder;
	
	}
	
	public function signalisationACharger()
	{
		$queryBuilder = $this->createQueryBuilder('i')
		->innerJoin('i.source', 'u')
		->leftJoin('i.action', 'ac')
		->innerJoin('i.instance', 'ins')
		->innerJoin('i.constatateur', 'd')
		->select('COUNT(ac) as nb')
		->where("nb != 0")
		->orderBy('i.id', 'ASC')
		->getQuery()->execute();
		return $queryBuilder;
	
	}
	
	public function statsGroupByCode(){
		return $this->filter()
					->addSelect('count(sign.id) total, sign.etatCourant ')
					->addGroupBy('sign.etatCourant');
	}
}

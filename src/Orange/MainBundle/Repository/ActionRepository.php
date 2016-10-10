<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use DoctrineExtensions\Query\Mysql\Week;
use Orange\MainBundle\Entity\Action;
use \DateTime;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\ArchitectureStructure;

class ActionRepository extends BaseRepository {
	public function ActionWithStructureNull(){
		return $this->createQueryBuilder('a')
						->leftJoin('a.instance', 'inst')
						->leftJoin('a.porteur', 'port')
						->leftJoin('a.typeAction', 'typ')
						->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
						->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
						->andWhere("a.structure IS NULL")
						->getQuery()
						->getResult();
	}
	public function getAllActions(){
		return $this->createQueryBuilder('a')
		->select('a.etatCourant AS type_statut, inst.id AS instance_id, '.date('W').' as semaine, dom.id AS domaine_id, typ.id AS type_action_id, COUNT(a.id) as nombre,
				port.id AS porteur_id, struct.id AS structure_id')
		->leftJoin('a.instance', 'inst')
		->leftJoin('a.porteur', 'port')
		->leftJoin('a.structure', 'struct')
		->leftJoin('a.domaine', 'dom')
		->leftJoin('a.typeAction', 'typ')
		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
		->groupBy('type_statut, instance_id, domaine_id, type_action_id, porteur_id, structure_id')
		->getQuery()
		->getResult();
	}
	public function findAll() {
		// TODO: Auto-generated method stub
			return $this->filter()->getQuery()->execute();
	}
	public function getActions($ids){
		return $this->createQueryBuilder('a')
		->where('a.id IN (:ids)')
		->setParameters(array('ids' => $ids))
		->getQuery()
		->getResult();
	}
	public function statistiqueUtilisateur($action_liste, $all) {
		$config = $this->getEntityManager()->getConfiguration();
		$config->addCustomDatetimeFunction('WEEK', 'DoctrineExtensions\Query\Mysql\Week');
		return $this->createQueryBuilder('a')
					->select('a.etatCourant AS type_statut, inst.id AS instance_id, '.date('W').' as semaine, dom.id AS domaine_id, typ.id AS type_action_id, COUNT(a.id) as nombre')
					->leftJoin('a.instance', 'inst')
					->leftJoin('a.domaine', 'dom')
					->leftJoin('a.typeAction', 'typ')
					->where('a.id IN (:action_liste)')
					->groupBy('type_statut, instance_id, domaine_id, type_action_id')
					->setParameters(array('action_liste' => $action_liste))
					->getQuery()
					->getResult();
	}
	
	public function userToAlertRappel($bu, $espace, $projet, $states) {
		$date =  date('Y-m-d');
		return $this->createQueryBuilder('a')
			->leftJoin('a.porteur', 'u')
			->where("a.etatReel LIKE 'ACTION_FAIT_DELAI' OR a.etatReel LIKE 'ACTION_FAIT_HORS_DELAI'")
			//->where('a.etatCourant LIKE :en_cours')
			->andWhere('a.dateInitial > :date')
			->andWhere('a.isDeleted = 0')
			->addOrderBy('a.dateAction', 'DESC')
			->setParameter('date', $date)
			//->setParameter('fait', "%ACT_DEMANDE_SOLDE%")
			//->setParameter('en_cours', "%ACT_TRAITEMENT%")
			->getQuery()
			->execute();
		
	}
	
	public function userToAlertAnimateur($bu, $espace, $projet)
	{
	
		$queryBuilder = $this->createQueryBuilder('a')
		->leftJoin('a.porteur', 'u')
		->leftJoin('a.instance', 'i')
		->leftJoin('i.animateur', 'an')
		->andWhere("a.etatReel LIKE 'ACTION_FAIT_DELAI' OR a.etatReel LIKE 'ACTION_FAIT_HORS_DELAI'")
		->orderBy('a.id', 'ASC')
		->addOrderBy('a.dateAction', 'DESC')
		->getQuery()
		->execute();
		return $queryBuilder;
	
	}
	
	public function alertAnimateurForReport($bu, $espace, $projet)
	{
	
		$queryBuilder = $this->createQueryBuilder('a')
		->leftJoin('a.porteur', 'u')
		->leftJoin('a.instance', 'i')
		->leftJoin('i.animateur', 'an')
		->andWhere("a.etatReel LIKE 'ACTION_DEMANDE_REPORT'")
		->orderBy('a.id', 'ASC')
		->addOrderBy('a.dateAction', 'DESC')
		->getQuery()
		->execute();
		return $queryBuilder;
	
	}
	
	public function alertAnimateurForAbandon($bu, $espace, $projet)
	{
	
		$queryBuilder = $this->createQueryBuilder('a')
		->leftJoin('a.porteur', 'u')
		->leftJoin('a.instance', 'i')
		->leftJoin('i.animateur', 'an')
		->andWhere("a.etatReel LIKE 'ACTION_DEMANDE_ABANDON'")
		->orderBy('a.id', 'ASC')
		->addOrderBy('a.dateAction', 'DESC')
		->getQuery()
		->execute();
		return $queryBuilder;
	
	}
	
	public function nouvelleAction($bu, $espace, $projet) {
		$date =  new \DateTime();
		return $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('a.instance', 'i')
			->innerJoin('a.domaine', 'd')
			->andWhere("a.etatReel LIKE 'ACTION_NOUVELLE'")
			->orderBy('a.id', 'ASC')
			->getQuery()
			->execute();	
	
	}
	
	public function userToAlertDepassement($bu, $espace, $projet){
		$date =  date('Y-m-d',  strtotime('+2 days'));
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('a.instance', 'i')
			->innerJoin('a.domaine', 'd')
			->where('a.dateInitial <= :date and a.dateInitial > CURRENT_DATE()')
			->andWhere("a.etatReel LIKE 'ACTION_NON_ECHUE'")
			->orderBy('a.id', 'ASC')
			->addOrderBy('a.dateAction', 'DESC')
			->setParameter('date', $date)
			->getQuery()
			->execute();
		return $queryBuilder;
	}
	
	
	public function alertQuartTime($bu, $espace, $projet){
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.porteur', 'u')
		->innerJoin('a.instance', 'i')
		->innerJoin('u.structure', 's')
		->innerJoin('s.buPrincipal', 'bu')
		->select('a.libelle libelle, a.id id, a.reference reference, i.libelle as lib_instance,
				u.prenom as prenom, u.nom as nom, a.dateDebut as dateDebut, a.dateInitial as dateInitial, u.email as email')
		->where('bu.id = 1')
		->andWhere("a.etatReel LIKE 'ACTION_NON_ECHUE'")
		->andWhere("a.etatCourant LIKE 'ACTION_NON_ECHUE'")
		->orderBy('a.id', 'ASC')
		->addOrderBy('a.dateAction', 'DESC')
		->getQuery()
		->getArrayResult();
		return $queryBuilder;
	}
	public function atteintDelai($bu, $espace, $projet){
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.porteur', 'u')
		->innerJoin('a.instance', 'i')
		->innerJoin('a.domaine', 'd')
		->where('a.dateInitial < CURRENT_DATE()')
		->andWhere("a.etatReel LIKE 'ACTION_ECHUE_NON_SOLDEE'")
		->orderBy('a.id', 'ASC')
		->addOrderBy('a.dateAction', 'DESC')
		->getQuery()
		->execute();
		return $queryBuilder;
	}
	public function getActionValide($code,$criteria){
			$queryBuilder= $this->filter()
    	->andWhere('a.etatReel!=:code')->setParameter('code', $code)
    	;
    	if(isset($criteria))
    		$this->filtres($queryBuilder, $criteria, 'a');
    		return $queryBuilder
    		->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'")
    		->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_MANAGER_ATTENTE'")
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	public function getActionValideForExport($code,$criteria){
		$queryBuilder= $this->filterExport()
		->andWhere('a.etatReel!=:code')->setParameter('code', $code)
		;
		if(isset($criteria))
			$this->filtres($queryBuilder, $criteria, 'a');
			return $queryBuilder
			->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE'")
			->andWhere("a.etatReel NOT LIKE 'EVENEMENT_VALIDATION_MANAGER_ATTENTE'")
			->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
			->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function listAllElementsForExport($criteria, $porteur = null) {
		$queryBuilder = $this->filterExport();
		$structure = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
		$instance = $criteria?$criteria->getInstance():null;
		$priorite = $criteria?$criteria->getPriorite():null;
		$type = $criteria?$criteria->getTypeAction():null;
		$porteur = $criteria ? $criteria->getPorteur():null;
		$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
		// 		$queryBuilder->select('partial a.{id, libelle, reference}');
		if($structure) {
			$queryBuilder
			->innerJoin('a.structure', 's')
			->andWhere('s.lvl >= :level')
			->andWhere('s.root = :root')
			->andWhere('s.lft >= :left')
			->andWhere('s.rgt <= :right')
			->setParameter('level', $structure->getLvl())
			->setParameter('root', $structure->getRoot())
			->setParameter('left', $structure->getLft())
			->setParameter('right', $structure->getRgt());
		}
		if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function myActions($criteria, $porteur = null) {
		$queryBuilder = $this->myFilter();
		$structure = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
		$instance = $criteria?$criteria->getInstance():null;
		$priorite = $criteria?$criteria->getPriorite():null;
		$type = $criteria?$criteria->getTypeAction():null;
		$porteur = $criteria ? $criteria->getPorteur():null;
		$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
		// 		$queryBuilder->select('partial a.{id, libelle, reference}');
		if($structure) {
			$queryBuilder
			->innerJoin('a.structure', 's')
			->andWhere('s.lvl >= :level')
			->andWhere('s.root = :root')
			->andWhere('s.lft >= :left')
			->andWhere('s.rgt <= :right')
			->setParameter('level', $structure->getLvl())
			->setParameter('root', $structure->getRoot())
			->setParameter('left', $structure->getLft())
			->setParameter('right', $structure->getRgt());
		}
		if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	
    /**
     * Methode utilise pour charger la liste des actions
     * @param unknown $criteria
     * @param unknown $porteur
     */
	public function listAllElements($criteria, $porteur = null) {
		$queryBuilder = $this->filter();
		$structure = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
		$instance = $criteria?$criteria->getInstance():null;
		$priorite = $criteria?$criteria->getPriorite():null;
		$type = $criteria?$criteria->getTypeAction():null;
		$porteur = $criteria ? $criteria->getPorteur():null;
		$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
		if($structure) {
			$queryBuilder
				->innerJoin('a.structure', 's')
				->andWhere('s.lvl >= :level')
				->andWhere('s.root = :root')
				->andWhere('s.lft >= :left')
				->andWhere('s.rgt <= :right')
				->setParameter('level', $structure->getLvl())
				->setParameter('root', $structure->getRoot())
				->setParameter('left', $structure->getLft())
				->setParameter('right', $structure->getRgt());
		}
		if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}

	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function listAllElementsGeneral() {
		$data = array();
		$queryBuilder = $this->filterGeneral();
		$queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
		$data= $queryBuilder->getQuery()->getArrayResult();
    	$data=$this->combineTacheAndAction($data);
    	return $data;;
	}
	
	public function listActionsByEspace($criteria,$id,$var, $id_user){
		$queryBuilder = $this->filter();
		$structure = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
    	$instance = $criteria?$criteria->getInstance():null;
    	$porteur = $criteria?$criteria->getPorteur():null;
    	$priorite = $criteria?$criteria->getPriorite():null;
    	$type = $criteria?$criteria->getTypeAction():null;
    	$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
		// 		$queryBuilder->select('partial a.{id, libelle, reference}');
		if($structure) {
			$queryBuilder
			->innerJoin('a.structure', 's')
			->andWhere('s.lvl >= :level')
			->andWhere('s.root = :root')
			->andWhere('s.lft >= :left')
			->andWhere('s.rgt <= :right')
			->setParameter('level', $structure->getLvl())
			->setParameter('root', $structure->getRoot())
			->setParameter('left', $structure->getLft())
			->setParameter('right', $structure->getRgt());
		}
		if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($instance) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $instance);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
		if($var==false) {
			$queryBuilder->innerJoin('a.porteur', 'p')
				->andWhere('espa.id=:id')->setParameter('id', $id)
				->andWhere('p.id=:id_user')->setParameter('id_user', $id_user);
		} else {
			$queryBuilder->andWhere('espa.id=:id')->setParameter('id', $id);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	public function listActionsByEspaceForExport($criteria,$id,$var, $id_user){
		$queryBuilder = $this->filterExport();
		$queryBuilder->innerJoin('a.instance', 'i');
		if($var==false) {
			$queryBuilder->innerJoin('a.porteur', 'p')
			->andWhere('esp.id=:id')->setParameter('id', $id)
			->andWhere('p.id=:id_user')->setParameter('id_user', $id_user);
		} else {
			$queryBuilder->andWhere('esp.id=:id')->setParameter('id', $id);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
	}
	public function allActionEspace($id){
		$queryBuilder = $this->createQueryBuilder('a');
		$queryBuilder->innerJoin('a.instance', 'i')
				->innerJoin('i.espace', 'e')
				->where('e.id=:id')->setParameter('id', $id);
		return $queryBuilder
			->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
			->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
			->getQuery()->execute();
	}
	public function listActionsUserByEspace($id_user,$id_espace){
		$queryBuilder = $this->createQueryBuilder('a');
		$queryBuilder->innerJoin('a.instance', 'i')
			->innerJoin('a.porteur', 'p')
			->innerJoin('i.espace', 'e')
			->where('e.id=:id')->setParameter('id', $id_espace)
			->andWhere('p.id=:id_user')->setParameter('id_user', $id_user);
		return $queryBuilder
			->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
			->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
			->getQuery()->execute();
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function filterForStruct() {
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.instance', 'insta')
		->leftJoin('insta.espace', 'espa')
		->leftJoin('a.signalisation', 'sign')
		->leftJoin('sign.source', 'src')
		->innerJoin('a.porteur', 'port')
		->leftJoin('a.priorite', 'priori')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
		->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			//$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIdsForAdmin()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function filterGeneral() {
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.instance', 'insta')
		->leftJoin('insta.espace', 'espa')
		->leftJoin('a.signalisation', 'sign')
		->leftJoin('sign.source', 'src')
		->innerJoin('a.porteur', 'port')
		->leftJoin('a.priorite', 'priori')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
		->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi')
		->select('count(a.id) total ,a.etatCourant action_etat')
		->groupBy('a.etatCourant');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$structure_id = $this->_user->getStructure()->getRoot();
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure( $this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id))));
				
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ANIMATEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_CHEF_PROJET)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIdsForChefProjet()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_MANAGER)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.porteur)', $this->_user->getCollaboratorsId()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
			$queryBuilder->orWhere('IDENTITY(a.porteur) = :userId');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_SOURCE)) {
			$queryBuilder->orWhere('IDENTITY(src.utilisateur) = :userId');
		}
		return $queryBuilder
			->setParameter('userId', $this->_user->getId());
	}
	/**
	 * @return QueryBuilder
	 */
	public function filter() {
		$queryBuilder = $this->createQueryBuilder('a')
			->innerJoin('a.instance', 'insta')
			->leftJoin('insta.espace', 'espa')
			->leftJoin('a.signalisation', 'sign')
			->leftJoin('sign.source', 'src')
			->innerJoin('a.porteur', 'port')
			->leftJoin('a.priorite', 'priori')
			->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
			->innerJoin('a.porteur', 'mp')
			->innerJoin('a.instance', 'mi');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$structure_id = $this->_user->getStructure()->getRoot();
			$structure=$this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
			$bu = $structure->getBuPrincipal();
			$idsInstances =array();
			foreach($bu->getInstance() as $inst)
				$idsInstances[] = $inst->getId();
				
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $idsInstances));
			//$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure( $this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id))));
			//$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getAllInstances($structure)));
			
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ANIMATEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIds()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_CHEF_PROJET)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.instance)', $this->_user->getInstanceIdsForChefProjet()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_RAPPORTEUR)) {
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getStructureIdsForRapporteur()));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_MANAGER)) {
			$structure = $this->_user->getStructure();
			$queryBuilder->orWhere($queryBuilder->expr()->in('IDENTITY(a.structure)', $this->_user->getChildrenForStructure($structure)));
		}
		if($this->_user->hasRole(Utilisateur::ROLE_PORTEUR)) {
			$queryBuilder->orWhere('IDENTITY(a.porteur) = :userId');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_SOURCE)) {
			$queryBuilder->orWhere('IDENTITY(src.utilisateur) = :userId');
		}
		return $queryBuilder->setParameter('userId', $this->_user->getId());
	}
	/**
	 * @return QueryBuilder
	 */
	public function filterAction() {
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.instance', 'insta')
		->leftJoin('insta.espace', 'espa')
		->leftJoin('a.signalisation', 'sign')
		->leftJoin('sign.source', 'src')
		->innerJoin('a.porteur', 'port')
		->leftJoin('a.priorite', 'priori')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
		->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi');
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function myFilter() {
		$queryBuilder = $this->createQueryBuilder('a')
		->innerJoin('a.instance', 'insta')
		->leftJoin('insta.espace', 'espa')
		->leftJoin('a.signalisation', 'sign')
		->leftJoin('sign.source', 'src')
		->innerJoin('a.porteur', 'port')
		->leftJoin('a.priorite', 'priori')
		->innerJoin('OrangeMainBundle:Statut', 'sr', 'WITH', 'sr.code = a.etatReel')
		->innerJoin('a.porteur', 'mp')->innerJoin('a.instance', 'mi')
		->where('port.id = :userId');
		return $queryBuilder->setParameter('userId', $this->_user->getId());
	}
	/**
	 * @return QueryBuilder
	 */
	public function filterExport() {
		return $this->filter()->select('partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial},
															partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
															partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
															partial cuser.{id, prenom, nom},partial port.{id, prenom, nom, structure}, 
															partial av.{id, description}, partial struct.{id},
															partial archStruct.{id, service, departement, pole, direction} ')
				->leftJoin('a.contributeur', 'cont')
				->leftJoin('insta.espace', 'esp')
				->leftJoin('cont.utilisateur', 'cuser')
				->leftJoin('a.avancement', 'av')
				->leftJoin('a.structure', 'struct')
				->leftJoin('struct.architectureStructure', 'archStruct')
				->innerJoin('a.typeAction', 'type')
				->leftJoin('a.domaine', 'dom')
				->groupBy('a.id');
	}
	public function filterExportReporting($idActions) {
		return $this->filterAction()->select('partial a.{id, libelle, reference, etatCourant, description, etatReel, dateDebut, dateFinExecut, dateInitial},
															partial insta.{id, libelle, couleur}, partial priori.{id, couleur, libelle},
															partial dom.{id, libelleDomaine}, partial type.{id, couleur, type}, partial cont.{id},
															partial cuser.{id, prenom, nom},partial port.{id, prenom, nom, structure},
															partial av.{id, description}, partial struct.{id},
															partial archStruct.{id, service, departement, pole, direction} ')
																->leftJoin('a.contributeur', 'cont')
																->leftJoin('insta.espace', 'esp')
																->leftJoin('cont.utilisateur', 'cuser')
																->leftJoin('a.avancement', 'av')
																->leftJoin('a.structure', 'struct')
																->leftJoin('struct.architectureStructure', 'archStruct')
																->innerJoin('a.typeAction', 'type')
																->leftJoin('a.domaine', 'dom')
																->groupBy('a.id')
																->andWhere('a.id IN (:ids)')->setParameter('ids', $idActions)->getQuery()->getResult();
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a1')->select('a1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a2')->select('DISTINCT(IDENTITY(a2.instance))')
			->innerJoin('a2.structure', 's2')
			->leftJoin('s2.buPrincipal', 'b21')
			->leftJoin('s2.bu', 'b22')
			->innerJoin('OrangeMainBundle:Bu', 'b2', 'WITH', 'b2 = b21 OR b2 = b22');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	public function adminQueryBuilder2(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a2')
		->innerJoin('a2.structure', 's2')
		->leftJoin('s2.buPrincipal', 'b21')
		->leftJoin('s2.bu', 'b22')
		->innerJoin('OrangeMainBundle:Bu', 'b2', 'WITH', 'b2 = b21 OR b2 = b22');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a3')->select('DISTINCT(i3.id)')
			->innerJoin('a3.porteur', 'u3')
			->innerJoin('a3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
 		$queryBuilder = $this->createQueryBuilder('a4')->select('DISTINCT(IDENTITY(a4.porteur))')
			->innerJoin('a4.structure', 's4');
 		$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a5')->select('DISTINCT(IDENTITY(so5.utilisateur))')
			->innerJoin('a5.signalisation', 's5')
			->innerJoin('s5.source', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a6')->select('DISTINCT(i6.id)')
			->innerJoin('a6.porteur', 'u6')
			->innerJoin('a6.instance', 'i6')
			->innerJoin('i6.chantier', 'c6')
			->innerJoin('c6.projet', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a7')->select('DISTINCT(IDENTITY(a7.porteur))')
				->innerJoin('a7.porteur', 'u7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'u7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder2(&$data = array()) {
		$queryBuilder =  $this->createQueryBuilder('a7')
			->innerJoin('a7.porteur', 'u7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'u7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	/**
	 * @return QueryBuilder
	 */
	public function rapporteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('a8')->select('DISTINCT(s8.id)')
		->innerJoin('a8.structure', 's8');
		$data = array_merge($this->filterByProfile($queryBuilder, 's8', Utilisateur::ROLE_RAPPORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	public function getNextId() {
		$data = $this->createQueryBuilder('a')
		->select('MAX(a.id) as maxi')
		->getQuery()->getArrayResult();
		return (int)$data[0]['maxi'] + 1;
	}
	
    /**
     *  Liste des actions dans lesquelles l\'utilisateur est 
     *  porteur
     */
    public function userActionPorteur($user_id)
    {
    	return $this->createQueryBuilder('q')
			    	->innerJoin('q.porteur', 'p')
			    	->where('p.id = :user_id')
			    	->setParameter('user_id', $user_id)
			    	->getQuery()
			    	->getResult();
    }
    
    /**
     *  Liste des actions dans lesquelles un utilisateur est impliqué mais
     *  dans un groupe
     */
    public function userActionGroup($user_id)
    {
    	return $this->createQueryBuilder('q')
    				  ->innerJoin('q.groupe', 'g')
    				  ->innerJoin('g.membreGroupe', 'm')
    				  ->innerJoin('m.utilisateur', 'u')
    				  ->where('u.id = :user_id')
    				  ->setParameter('user_id', $user_id)
    				  ->getQuery()
    				  ->getResult();
    }
    
    /**
     *  Liste des actions dans lesquelles un utilisateur est impliqué mais
     *  en tant que contributeur
     */
    public function userActionContributeur($user_id)
    {
    	 
    	return $this->createQueryBuilder('q')
			    	->innerJoin('q.contributeur', 'c')
			    	->innerJoin('c.utilisateur', 'u')
			    	->where('u.id = :user_id')
			    	->setParameter('user_id', $user_id)
			    	->getQuery()
			    	->getResult();
    }
  
    /**
     * 
     * @param unknown $structure_id
     */
    public function getActionByStructure($structure_id)
    {
    	$queryBuilder = $this->filter();
    	return $queryBuilder
    	->where('IDENTITY(a.structure)=:structure_id')
    	->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    	->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
    	->setParameter('structure_id',$structure_id)
    	->getQuery()
    	->getResult();
    }
    
    /**
     *Les actions des collaborateurs d'une structure
     * @param unknown $structure_id
     */
    public function getActionCollaborateursForExport($criteria)
    {
    	$structure=$this->_user->getStructure();
    	$domaine = $criteria?$criteria->getDomaine():null;
    	$instance = $criteria?$criteria->getInstance():null;
    	$priorite = $criteria?$criteria->getPriorite():null;
    	$type = $criteria?$criteria->getTypeAction():null;
    	$porteur = $criteria ? $criteria->getPorteur():null;
    	$toDeb = $criteria?$criteria->hasToDebut():null;
    	$fromDeb = $criteria?$criteria->hasFromDebut():null;
    	$toInit = $criteria?$criteria->hasToInitial():null;
    	$fromInit = $criteria?$criteria->hasFromInitial():null;
    	$toClot = $criteria?$criteria->hasToCloture():null;
    	$statut = $criteria?$criteria->hasStatut():null;
    	$fromClot = $criteria?$criteria->hasFromCloture():null;
    	
    	$queryBuilder = $this->filterExport();
    	$queryBuilder->innerJoin('a.porteur', 'u')
    	->innerJoin('a.structure','s')
    	->andWhere('u!=:userid')->setParameter('userid', $this->_user)
    	->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
    	->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
    	->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
    	->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
    	if(isset($domaine)) {
    		$queryBuilder->andWhere('a.domaine = :domaine')
    		->setParameter('domaine', $domaine);
    	}
    	if(isset($instance)) {
    		$queryBuilder->andWhere('a.instance = :instance')
    		->setParameter('instance', $instance);
    	}
    	if(isset($porteur)) {
    		$queryBuilder->andWhere('a.porteur = :porteur')
    		->setParameter('porteur', $porteur);
    	}
    	if(isset($type)) {
    		$queryBuilder->andWhere('a.typeAction = :type')
    		->setParameter('type', $type);
    	}
    	if($priorite) {
    		$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
    	}
    	if(isset($statut)) {
    		$queryBuilder->andWhere('a.etatReel = :code')
    		->setParameter('code', $statut->getCode());
    	}
    	if(isset($fromDeb)) {
    		$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')
    		->setParameter('to', $toDeb)
    		->setParameter('from', $fromDeb);
    	}
    	if(isset($fromInit)) {
    		$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')
    		->setParameter('to', $toInit)
    		->setParameter('from', $fromInit);
    	}
    	if(isset($fromClot)) {
    		$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')
    		->setParameter('to', $toClot)
    		->setParameter('from', $fromClot);
    	}
    	return $queryBuilder
    	->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    	->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    
    /**
     *Les actions des collaborateurs d'une structure
     * @param unknown $structure_id
     */
    public function getActionCollaborateurs($criteria)
    {
    	$structure=$this->_user->getStructure();
    	$domaine = $criteria?$criteria->getDomaine():null;
    	$instance = $criteria?$criteria->getInstance():null;
    	$porteur = $criteria?$criteria->getPorteur():null;
    	$priorite = $criteria?$criteria->getPriorite():null;
    	$type = $criteria?$criteria->getTypeAction():null;
    	$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
    	$queryBuilder = $this->filter();
    	$queryBuilder->innerJoin('a.porteur', 'u')
    	->innerJoin('a.structure','s')
    	->andWhere('u!=:userid')->setParameter('userid', $this->_user)
    	->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
    	->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
    	->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
    	->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
    	if(isset($domaine)) {
    		$queryBuilder->andWhere('a.domaine = :domaine')
    		->setParameter('domaine', $domaine);
    	}
    	if(isset($instance)) {
    		$queryBuilder->andWhere('a.instance = :instance')
    		->setParameter('instance', $instance);
    	}
    	if(isset($porteur)) {
    		$queryBuilder->andWhere('a.porteur = :porteur')
    		->setParameter('porteur', $porteur);
    	}
    	if(isset($type)) {
    		$queryBuilder->andWhere('a.typeAction = :type')
    		->setParameter('type', $type);
    	}
    	if($priorite) {
    		$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
    	}
    	if(isset($statut)) {
    		$queryBuilder->andWhere('a.etatReel = :code')
    		->setParameter('code', $statut->getCode());
    	}
    	if(isset($fromDeb)) {
    		$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')
    		->setParameter('to', $toDeb)
    		->setParameter('from', $fromDeb);
    	}
    	if(isset($fromInit)) {
    		$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')
    		->setParameter('to', $toInit)
    		->setParameter('from', $fromInit);
    	}
    	if(isset($fromClot)) {
    		$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')
    		->setParameter('to', $toClot)
    		->setParameter('from', $fromClot);
    	}
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    
    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByStructForExport($structure_id,$criteria)
    {
    	$structure=$this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
    	$queryBuilder = $this->filterExport();
    	$struct = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
		$instance = $criteria?$criteria->getInstance():null;
		$priorite = $criteria?$criteria->getPriorite():null;
		$type = $criteria?$criteria->getTypeAction():null;
		$porteur = $criteria ? $criteria->getPorteur():null;
		$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
    	if ($structure){
    		$queryBuilder
    		->leftJoin('a.structure','s')
    		->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
    		->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
    		->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
    		->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
    	}
    	if($struct) {
    			$queryBuilder
    			->innerJoin('a.structure', 'st')
    			->andWhere('st.lvl >= :level')
    			->andWhere('st.root = :root')
    			->andWhere('st.lft >= :left')
    			->andWhere('st.rgt <= :right')
    			->setParameter('level', $struct->getLvl())
    			->setParameter('root', $struct->getRoot())
    			->setParameter('left', $struct->getLft())
    			->setParameter('right', $struct->getRgt());
    		}
    		if($domaine) {
    			$queryBuilder->andWhere('a.domaine = :domaine')
    			->setParameter('domaine', $domaine);
    		}
    		if($instance) {
    			$queryBuilder->andWhere('a.instance = :instance')
    			->setParameter('instance', $instance);
    		}
    		if($porteur) {
    			$queryBuilder->andWhere('a.porteur = :porteur')
    			->setParameter('porteur', $porteur);
    		}
    		if($type) {
    			$queryBuilder->andWhere('a.typeAction = :type')
    			->setParameter('type', $type);
    		}
    		if($priorite) {
    			$queryBuilder->andWhere('a.priorite = :priorite')
    			->setParameter('priorite', $priorite);
    		}
    		if($statut) {
    			$queryBuilder->andWhere('a.etatReel = :code')
    			->setParameter('code', $statut->getCode());
    		}
    		if($fromDeb) {
    			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')
    			->setParameter('to', $toDeb)
    			->setParameter('from', $fromDeb);
    		}
    		if($fromInit) {
    			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')
    			->setParameter('to', $toInit)
    			->setParameter('from', $fromInit);
    		}
    		if($fromClot) {
    			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')
    			->setParameter('to', $toClot)
    			->setParameter('from', $fromClot);
    		}
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
 	/**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByStruct($structure_id,$criteria)
    {
    	$structure=$this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
    	$queryBuilder = $this->filter();
    	$struct = $criteria?$criteria->getStructure():null;
		$domaine = $criteria?$criteria->getDomaine():null;
    	$instance = $criteria?$criteria->getInstance():null;
    	$porteur = $criteria?$criteria->getPorteur():null;
    	$priorite = $criteria?$criteria->getPriorite():null;
    	$type = $criteria?$criteria->getTypeAction():null;
    	$toDeb = $criteria?$criteria->hasToDebut():null;
		$fromDeb = $criteria?$criteria->hasFromDebut():null;
		$toInit = $criteria?$criteria->hasToInitial():null;
		$fromInit = $criteria?$criteria->hasFromInitial():null;
		$toClot = $criteria?$criteria->hasToCloture():null;
		$statut = $criteria?$criteria->hasStatut():null;
		$fromClot = $criteria?$criteria->hasFromCloture():null;
    	if (!isset($struct)){
    		$queryBuilder
    		->leftJoin('a.structure','s')
    		->andWhere('s.lvl >= :lvl')->setParameter('lvl', $structure->getLvl())
    		->andWhere('s.root = :root')->setParameter('root', $structure->getRoot())
    		->andWhere('s.lft  >= :lft')->setParameter('lft', $structure->getLft())
    		->andWhere('s.rgt <= :rgt')->setParameter('rgt', $structure->getRgt());
    	}
    	if(isset($struct)) {
    		$queryBuilder
    		->innerJoin('a.structure', 's')
    		->andWhere('s.lvl >= :level')
    		->andWhere('s.root = :root')
    		->andWhere('s.lft >= :left')
    		->andWhere('s.rgt <= :right')
    		->setParameter('level', $struct->getLvl())
    		->setParameter('root', $struct->getRoot())
    		->setParameter('left', $struct->getLft())
    		->setParameter('right', $struct->getRgt());
    	}
    	if(isset($domaine)) {
    		$queryBuilder->andWhere('a.domaine = :domaine')
    		->setParameter('domaine', $domaine);
    	}
    	if(isset($instance)) {
    		$queryBuilder->andWhere('a.instance = :instance')
    		->setParameter('instance', $instance);
    	}
    	if(isset($porteur)) {
    		$queryBuilder->andWhere('a.porteur = :porteur')
    		->setParameter('porteur', $porteur);
    	}
    	if(isset($type)) {
    		$queryBuilder->andWhere('a.typeAction = :type')
    		->setParameter('type', $type);
    	}
    	if($priorite) {
    		$queryBuilder->andWhere('a.priorite = :priorite')
    		->setParameter('priorite', $priorite);
    	}
    	if(isset($statut)) {
    		$queryBuilder->andWhere('a.etatReel = :code')
    		->setParameter('code', $statut->getCode());
    	}
    	if(isset($fromDeb)) {
    		$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')
    		->setParameter('to', $toDeb)
    		->setParameter('from', $fromDeb);
    	}
    	if(isset($fromInit)) {
    		$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')
    		->setParameter('to', $toInit)
    		->setParameter('from', $fromInit);
    	}
    	if(isset($fromClot)) {
    		$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')
    		->setParameter('to', $toClot)
    		->setParameter('from', $fromClot);
    	}
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    
    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByInstance($instance_id,$criteria)
    {
	    	$structure = $criteria?$criteria->getStructure():null;
	    	$domaine = $criteria?$criteria->getDomaine():null;
	    	$instance = $criteria?$criteria->getInstance():null;
	    	$porteur = $criteria?$criteria->getPorteur():null;
	    	$priorite = $criteria?$criteria->getPriorite():null;
	    	$type = $criteria?$criteria->getTypeAction():null;
	    	$toDeb = $criteria?$criteria->hasToDebut():null;
	    	$fromDeb = $criteria?$criteria->hasFromDebut():null;
	    	$toInit = $criteria?$criteria->hasToInitial():null;
	    	$fromInit = $criteria?$criteria->hasFromInitial():null;
	    	$toClot = $criteria?$criteria->hasToCloture():null;
	    	$statut = $criteria?$criteria->hasStatut():null;
	    	$fromClot = $criteria?$criteria->hasFromCloture():null;
	    	$queryBuilder= $this->filter()
	    		->innerJoin('a.instance', 'i');
	    	if(!$instance){
	    		$queryBuilder
		    		->andWhere('i.id=:instance_id')
		    		->setParameter('instance_id',$instance_id);
	    	}
    		if($structure) {
    			$queryBuilder
    			->innerJoin('a.structure', 's')
    			->andWhere('s.lvl >= :level')
    			->andWhere('s.root = :root')
    			->andWhere('s.lft >= :left')
    			->andWhere('s.rgt <= :right')
    			->setParameter('level', $structure->getLvl())
    			->setParameter('root', $structure->getRoot())
    			->setParameter('left', $structure->getLft())
    			->setParameter('right', $structure->getRgt());
    		}
    		if($domaine) {
    			$queryBuilder->andWhere('a.domaine = :domaine')
    			->setParameter('domaine', $domaine);
    		}
    		if($instance) {
    			$queryBuilder->andWhere('a.instance = :instance')
    			->setParameter('instance', $instance);
    		}
    		if($porteur) {
    			$queryBuilder->andWhere('a.porteur = :porteur')
    			->setParameter('porteur', $porteur);
    		}
    		if($type) {
    			$queryBuilder->andWhere('a.typeAction = :type')
    			->setParameter('type', $type);
    		}
    		if($priorite) {
    			$queryBuilder->andWhere('a.priorite = :priorite')
    			->setParameter('priorite', $priorite);
    		}
    		if($statut) {
    			$queryBuilder->andWhere('a.etatReel = :code')
    			->setParameter('code', $statut->getCode());
    		}
    		if($fromDeb) {
    			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')
    			->setParameter('to', $toDeb)
    			->setParameter('from', $fromDeb);
    		}
    		if($fromInit) {
    			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')
    			->setParameter('to', $toInit)
    			->setParameter('from', $fromInit);
    		}
    		if($fromClot) {
    			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')
    			->setParameter('to', $toClot)
    			->setParameter('from', $fromClot);
    		}
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
     
    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByInstanceForExport($instance_id,$criteria)
    {
    	$instance =$this->_em->getRepository('OrangeMainBundle:Instance')->find($instance_id);
    	$structure = $criteria?$criteria->getStructure():null;
    	$domaine = $criteria?$criteria->getDomaine():null;
    	$inst = $criteria?$criteria->getInstance():null;
    	$porteur = $criteria?$criteria->getPorteur():null;
    	$priorite = $criteria?$criteria->getPriorite():null;
    	$type = $criteria?$criteria->getTypeAction():null;
    	$toDeb = $criteria?$criteria->hasToDebut():null;
    	$fromDeb = $criteria?$criteria->hasFromDebut():null;
    	$toInit = $criteria?$criteria->hasToInitial():null;
    	$fromInit = $criteria?$criteria->hasFromInitial():null;
    	$toClot = $criteria?$criteria->hasToCloture():null;
    	$statut = $criteria?$criteria->hasStatut():null;
    	$fromClot = $criteria?$criteria->hasFromCloture():null;
    	$queryBuilder= $this->filterExport();
    	if($instance){
    		$queryBuilder
    		->andWhere('a.instance = :instance')
    		->setParameter('instance',$instance);
    	}
    	if($structure) {
    		$queryBuilder
    		->innerJoin('a.structure', 's')
    		->andWhere('s.lvl >= :level')
    		->andWhere('s.root = :root')
    		->andWhere('s.lft >= :left')
    		->andWhere('s.rgt <= :right')
    		->setParameter('level', $structure->getLvl())
    		->setParameter('root', $structure->getRoot())
    		->setParameter('left', $structure->getLft())
    		->setParameter('right', $structure->getRgt());
    	}
    	if($domaine) {
			$queryBuilder->andWhere('a.domaine = :domaine')->setParameter('domaine', $domaine);
		}
		if($inst) {
			$queryBuilder->andWhere('a.instance = :instance')->setParameter('instance', $inst);
		}
		if($porteur) {
			$queryBuilder->andWhere('a.porteur = :porteur')->setParameter('porteur', $porteur);
		}
		if($type) {
			$queryBuilder->andWhere('a.typeAction = :type')->setParameter('type', $type);
		}
		if($priorite) {
			$queryBuilder->andWhere('a.priorite = :priorite')->setParameter('priorite', $priorite);
		}
		if($statut) {
			$queryBuilder->andWhere('a.etatReel = :code')->setParameter('code', $statut->getCode());
		}
		if($fromDeb) {
			$queryBuilder->andWhere('a.dateDebut >= :from and a.dateDebut <= :to')->setParameter('to', $toDeb)->setParameter('from', $fromDeb);
		}
		if($fromInit) {
			$queryBuilder->andWhere('a.dateInitial >= :from and a.dateInitial <= :to')->setParameter('to', $toInit)->setParameter('from', $fromInit);
		}
		if($fromClot) {
			$queryBuilder->andWhere('a.dateCloture >= :from and a.dateCloture <= :to')->setParameter('to', $toClot)->setParameter('from', $fromClot);
		}
		return $queryBuilder->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByCodeStatut($code_statut,$criteria)
    {
    	$queryBuilder= $this->filter()
    	->andWhere('a.etatCourant=:code')->setParameter('code', $code_statut)
    	;
    	if(isset($criteria))
    		$this->filtres($queryBuilder, $criteria, 'a');
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    /**
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActionByCodeStatutForExport($code_statut,$criteria)
    {
    	$queryBuilder= $this->filterExport()
    	->andWhere('a.etatCourant=:code')->setParameter('code', $code_statut)
    	;
    	if(isset($criteria))
    		$this->filtres($queryBuilder, $criteria, 'a');
    		return $queryBuilder
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE'")
    		->andWhere("a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'");
    }
    /* ------------------------Stats ------------------------  */
    
    public function combineTacheAndActionComplexe($data){
    	$arrData=array();
    	$i=0;
    	if(count($data)>0)
    		foreach($data as $key =>$value){
    			if (count($arrData)<=0){
    				$arrData[$i]=array('s_id'=>$value['s_id'], 's_libelle'=>$value['s_libelle'],'f_id'=>$value['f_id'], 'f_libelle'=>$value['f_libelle'], 'total'=>intval($value['total']));
    				if ($value['tache_etat']==null)
    					$arrData[$i]['etatCourant']=$value['action_etat'];
    					else
    						$arrData[$i]['etatCourant']=$value['tache_etat'];
    			}else{
    				$aide=false;
    				for ($j=0; $j<count($arrData);$j++){
    					if ($value['tache_etat']==null){
    						if ($arrData[$j]['etatCourant']==$value['action_etat'] && $arrData[$j]['f_id']==$value['f_id'] && $arrData[$j]['s_id']==$value['s_id']){
    							$arrData[$j]['total']+=intval($value['total']);
    							$aide=true;
    							break;
    						}
    					}else{
    						if ($arrData[$j]['etatCourant']==$value['tache_etat'] && $arrData[$j]['f_id']==$value['f_id'] && $arrData[$j]['s_id']==$value['s_id']){
    							$arrData[$j]['total']+=intval($value['total']);
    							$aide=true;
    							break;
    						}
    					}
    				}
    				if($aide==false){
    					$i++;
    					$arrData[$i]=array('s_id'=>$value['s_id'], 's_libelle'=>$value['s_libelle'],'f_id'=>$value['f_id'], 'f_libelle'=>$value['f_libelle'], 'total'=>intval($value['total']));
    					if ($value['tache_etat']==null)
    						$arrData[$i]['etatCourant']=$value['action_etat'];
    						else
    							$arrData[$i]['etatCourant']=$value['tache_etat'];
    				}
    		 		
    			}
    	}
    	return $arrData;
    }
    
    /**
     *
     * @param unknown $user
     */
    public function getStatsByInstance2($role,$criteria){
    	$queryBuilder=null;
    	$data=array();
    	if($role==Utilisateur::ROLE_ADMIN)
    		$queryBuilder=$this->adminQueryBuilder($data);
    	elseif ($role==Utilisateur::ROLE_ANIMATEUR)
    		$queryBuilder=$this->animateurQueryBuilder($data);
    	elseif ($role===Utilisateur::ROLE_MANAGER){
    	    $queryBuilder=$this->managerQueryBuilder($data);
    	}elseif($role=== Utilisateur::ROLE_PORTEUR)
    		$queryBuilder=$this->porteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$queryBuilder=$this->rapporteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_CONTRIBUTEUR){
    		$queryBuilder=$this->createQueryBuilder('a')
    						   ->innerJoin('a.contributeur', 'c');
    		$queryBuilder=$this->filterByProfile($queryBuilder, 'c', Utilisateur::ROLE_CONTRIBUTEUR);
    		
    	}else{
    		$queryBuilder=$this->filter();
    	}
    	$alias=$queryBuilder->getRootAlias();
    	$queryBuilder
					    	->leftJoin($alias.'.actionCyclique', 'acl1')
					    	->leftJoin('acl1.tache', 't1')
					    	->leftJoin($alias.'.instance', 'i')
					    	->innerJoin($alias.'.porteur','u')
					    	->innerJoin($alias.'.structure', 's')
					    	->andWhere($alias.".etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE' AND ".$alias.".etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
					    	->select($alias.'.id')
    						;
		if ($role===Utilisateur::ROLE_MANAGER)
			$queryBuilder->andWhere('u!=:me')->setParameter('me', $this->_user);
    	$this->filtres($queryBuilder, $criteria, $alias);
    	return $queryBuilder;
    }
    /**
     * Recuperer les stats des structures en parametre groupés statut
     */
    public function getStatsByStructure2($role,$criteria){
    	$criteria=($criteria)?$criteria:new \Orange\MainBundle\Entity\Action();
    	$structures=null;
    	$instances=null;
    	$data=array();
    	$user=$this->_user;
    	$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    	if($role===Utilisateur::ROLE_ADMIN)
    		$structures=$rep->adminQueryBuilder($data)->addSelect('s2.libelle')->getQuery()->getArrayResult();
    		elseif($role===Utilisateur::ROLE_ANIMATEUR){
    			$instances=$this->_em
    			->getRepository('OrangeMainBundle:Instance')
    			->getInstanceByRole(Utilisateur::ROLE_ANIMATEUR)->getQuery()->getArrayResult();
    		}elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$structures=$rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
    		elseif ($role===Utilisateur::ROLE_MANAGER){
    			$structures=$rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
    		}else{
    			$queryBuilder=$this->filter();
    		}
    		$queryBuilder =$rep->createQueryBuilder('s')
    		->select('a.id')
    		->leftJoin('OrangeMainBundle:Action ','a', 'WITH', '1=1')
    		->leftJoin('a.actionCyclique', 'acl')
    		->leftJoin('acl.tache', 't')
    		->innerJoin('a.porteur','u')
    		->innerJoin('a.instance', 'i')
    		->innerJoin('a.structure','s1')
    		->andWhere('s1.lvl >= s.lvl')
    		->andWhere('s1.root = s.root')
    		->andWhere('s1.lft  >= s.lft')
    		->andWhere('s1.rgt <= s.rgt')
    		->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE' AND a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
    		;
    		if($role==Utilisateur::ROLE_ANIMATEUR){
    			$instancesIds=array();
    			foreach ($instances as $key=>$data)
    				$instancesIds[]=\is_object($data)?$data->getId():$data['id'];
    				$queryBuilder->andWhere('i.id in (:insts)')->setParameter('insts', $instancesIds);
    		}else{
    			$structureIds=array();
    			foreach ($structures as $key=>$data)
    				$structureIds[]=\is_object($data)?$data->getId():$data['id'];
    				$queryBuilder->andWhere('s.id in (:structs)')->setParameter('structs', $structureIds);
    		}
    
    		if($role==Utilisateur::ROLE_MANAGER)
    			$queryBuilder->andWhere('u != :me')->setParameter('me', $this->_user);
    			$this->filtres($queryBuilder, $criteria, 'a');
    			return $queryBuilder->groupBy('a.id');
    }
    /**
     *
     * @param unknown $user
     */
    public function getStatsByInstance($role,$criteria){
    	$queryBuilder=null;
    	$data=array();
    	if($role==Utilisateur::ROLE_ADMIN)
    		$queryBuilder=$this->adminQueryBuilder($data);
    	elseif ($role==Utilisateur::ROLE_ANIMATEUR)
    		$queryBuilder=$this->animateurQueryBuilder($data);
    	elseif ($role===Utilisateur::ROLE_MANAGER){
    	    $queryBuilder=$this->managerQueryBuilder($data);
    	}elseif($role=== Utilisateur::ROLE_PORTEUR)
    		$queryBuilder=$this->porteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$queryBuilder=$this->rapporteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_CONTRIBUTEUR){
    		$queryBuilder=$this->createQueryBuilder('a')
    						   ->innerJoin('a.contributeur', 'c');
    		$queryBuilder=$this->filterByProfile($queryBuilder, 'c', Utilisateur::ROLE_CONTRIBUTEUR);
    		
    	}else{
    		$queryBuilder=$this->filter();
    	}
    	$alias=$queryBuilder->getRootAlias();
    	$queryBuilder
					    	->leftJoin($alias.'.actionCyclique', 'acl1')
					    	->leftJoin('acl1.tache', 't1')
					    	->leftJoin($alias.'.instance', 'i')
					    	->innerJoin($alias.'.porteur','u')
					    	->innerJoin($alias.'.structure', 's')
					    	->select('count('.$alias.'.id) total ,'.$alias.'.etatCourant action_etat, t1.etatCourant tache_etat, i.id, i.libelle')
					    	->groupBy('i.id')->addGroupBy($alias.'.etatCourant')->addGroupBy('t1.etatCourant')
    						;
		if ($role===Utilisateur::ROLE_MANAGER)
			$queryBuilder->andWhere('u!=:me')->setParameter('me', $this->_user);
    	$this->filtres($queryBuilder, $criteria, $alias);
    	return $queryBuilder;
    }
    
   
    /**
     * Recuperer les stats des structures en parametre groupés statut 
     */
 	public function getStatsByStructure($role,$criteria){
 		$criteria=($criteria)?$criteria:new \Orange\MainBundle\Entity\Action();
 		$structures=null;
 		$instances=null;
 		$data=array();
 		$user=$this->_user;
 		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    	if($role===Utilisateur::ROLE_ADMIN)
   			$structures=$rep->adminQueryBuilder($data)->addSelect('s2.libelle')->getQuery()->getArrayResult();
    	elseif($role===Utilisateur::ROLE_ANIMATEUR){
    		$instances=$this->_em
    						->getRepository('OrangeMainBundle:Instance')
    						->getInstanceByRole(Utilisateur::ROLE_ANIMATEUR)->getQuery()->getArrayResult();
    	}elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$structures=$rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
    	elseif ($role===Utilisateur::ROLE_MANAGER){
    		$structures=$rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
    	}else{
    		$queryBuilder=$this->filter();
    	}
    	$queryBuilder =$rep->createQueryBuilder('s')
					    	->select('a.etatCourant action_etat, COUNT(a.id) total,t.etatCourant tache_etat ,s.libelle, s.id')
					    	->leftJoin('OrangeMainBundle:Action ','a', 'WITH', '1=1')
					    	->leftJoin('a.actionCyclique', 'acl')
					    	->leftJoin('acl.tache', 't')
					    	->innerJoin('a.porteur','u')
					    	->innerJoin('a.instance', 'i')
					    	->innerJoin('a.structure','s1')
					    	->andWhere('s1.lvl >= s.lvl')
					    	->andWhere('s1.root = s.root')
					    	->andWhere('s1.lft  >= s.lft')
					    	->andWhere('s1.rgt <= s.rgt')
					    	->andWhere("a.etatCourant NOT LIKE 'ABANDONNEE_ARCHIVEE' AND a.etatCourant NOT LIKE 'SOLDEE_ARCHIVEE'")
    						;
    	if($role==Utilisateur::ROLE_ANIMATEUR){
    		$instancesIds=array();
    		foreach ($instances as $key=>$data)
    			$instancesIds[]=\is_object($data)?$data->getId():$data['id'];
    		$queryBuilder->andWhere('i.id in (:insts)')->setParameter('insts', $instancesIds);
    	}else{
    		$structureIds=array();
    		foreach ($structures as $key=>$data)
    			$structureIds[]=\is_object($data)?$data->getId():$data['id'];
    		$queryBuilder->andWhere('s.id in (:structs)')->setParameter('structs', $structureIds);
    	}
    		
    	if($role==Utilisateur::ROLE_MANAGER)
    		$queryBuilder->andWhere('u != :me')->setParameter('me', $this->_user);
    	$this->filtres($queryBuilder, $criteria, 'a');
    	return $queryBuilder->groupBy('s.id')->addGroupBy('a.etatCourant')->orderBy('s.lvl');
    }
    
    /**
     * Recuperer les stats des structures en parametre groupés par instance , statut 
     * @param unknown $criteria
     * @param unknown $structures
     */
    public function getStatsByStructureInstance($role,$criteria){
    	$criteria=($criteria)?$criteria:new \Orange\MainBundle\Entity\Action();
 		$structures=null;
 		$data=array();
 		$user=$this->_user;
 		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
    	if($role===Utilisateur::ROLE_ADMIN)
   			$structures=$rep->getStructureAndStructureDirecteByStructure($this->_user->getStructure()->getRoot())->getQuery()->getArrayResult();
    	elseif($role===Utilisateur::ROLE_ANIMATEUR)
    		$structures=$rep->animateurQueryBuilder($data)->addSelect('s3.libelle')->getQuery()->getArrayResult();
    	elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$structures=$rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
    	elseif ($role===Utilisateur::ROLE_MANAGER)
    		$structures=$rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
    	
    	$structureIds=array();
    	foreach ($structures as $key=>$data)
    		$structureIds[]=\is_object($data)?$data->getId():$data['id'];
    	$queryBuilder =$rep ->createQueryBuilder('s')
					    	->select('a.etatCourant action_etat, t.etatCourant tache_etat ,COUNT(a.id) total ,s.libelle f_libelle, i.libelle inst, i.libelle s_libelle, s.id f_id,i.id s_id')
					    	->add('from', 'OrangeMainBundle:Action a', true)
					    	->leftJoin('a.actionCyclique', 'acl')
					    	->leftJoin('acl.tache', 't')
					    	->innerJoin('a.structure','s1')
					    	->innerJoin('a.instance', 'i')
					    	->innerJoin('i.bu', 'b')
					    	->leftJoin('i.espace', 'e')
					    	->where('s.id in (:structs)')->setParameter('structs', $structureIds)
					    	->andWhere('s1.lvl >= s.lvl')
					    	->andWhere('s1.root = s.root')
					    	->andWhere('s1.lft  >= s.lft')
					    	->andWhere('s1.rgt <= s.rgt')
					    	->andWhere('b.id=s.buPrincipal')
					    	->andWhere(' e.id IS NULL');
    	
    	$this->filtres($queryBuilder, $criteria, 'a');
    	$data= $queryBuilder->groupBy('f_id')->addGroupBy('s_id')->addGroupBy('action_etat')->addGroupBy('tache_etat')->getQuery()->getArrayResult();
    	$data=$this->combineTacheAndActionComplexe($data);
    	return $data;
    }
    
    
    public function userActionContributionByInstance($instance_id)
    {
    	$datas= $this->createQueryBuilder('a')
    	->innerJoin('a.contributeur', 'c')
    	->innerJoin('c.utilisateur', 'u')
    	->where('IDENTITY(a.instance) = :intance_id')
    	->setParameter('intance_id', $instance_id)
    	;
//     	$this->valider($datas,'a');
    	return $datas;
    }
    
    
    /**
     * gerer les filtres
     *
     */
    public function filtres($queryBuilder, $criteria, $alias){
    	$criteria=($criteria)?$criteria:new \Orange\MainBundle\Entity\Action();
    		if($criteria->getPorteur()){
    			$queryBuilder->andWhere($alias.'.porteur = :porteur')->setParameter('porteur', $criteria->getPorteur());
    		}
    		if(count($criteria->instances)>0){
    			$instIDs=array();
    			foreach ($criteria->instances as $key=>$val)
    				$instIDs[]=$val->getId();
    			$queryBuilder->andWhere($alias.'.instance in (:instanceIds)')->setParameter('instanceIds', $instIDs);
    		}
    		if($criteria->getDomaine()){
    			$queryBuilder->andWhere($alias.'.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
    		}
    		if($criteria->getTypeAction()){
    			$queryBuilder->andWhere($alias.'.typeAction = :type_action')->setParameter('type_action', $criteria->getTypeAction());
    		}
    		if($criteria->getStructure()){
    			$structure=$criteria->getStructure();
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
    		if($criteria->statut) {
    			$queryBuilder->andWhere($alias.'.etatCourant = :code')
    			->setParameter('code', $criteria->statut);
    		}
    		if($criteria->hasFromDebut()) {
    			$queryBuilder->andWhere($alias.'.dateDebut >= :from and a.dateDebut <= :to')
    			->setParameter('to', $criteria->hasToDebut())
    			->setParameter('from', $criteria->hasFromDebut());
    		}
    		if($criteria->hasFromInitial()) {
    			$queryBuilder->andWhere($alias.'.dateInitial >= :from and a.dateInitial <= :to')
    			->setParameter('to', $criteria->hasToInitial())
    			->setParameter('from',  $criteria->hasFromInitial());
    		}
    		if($criteria->hasFromCloture()) {
    			$queryBuilder->andWhere($alias.'.dateCloture >= :from and a.dateCloture <= :to')
    			->setParameter('to', $criteria->hasToCloture())
    			->setParameter('from', $criteria->hasFromCloture());
    		}
	}
    /**
     * 
     * Filter les  actions validees
     */
    
    public function actionEchue() {
    	return $this->createQueryBuilder('q')
	    	->where("q.etatReel = 'ACTION_NON_ECHUE' and q.etatCourant = 'ACTION_NON_ECHUE'")
	    	->andWhere('q.dateInitial < :now')
	    	->setParameter('now',date('Y-m-d'))
	    	->getQuery()
	    	->getResult();
    }

    public function actionNonEchue() {
    	return $this->createQueryBuilder('q')
    	->where("q.etatReel = 'ACTION_ECHUE_NON_SOLDEE' and q.etatCourant = 'ACTION_ECHUE_NON_SOLDEE'")
    	->andWhere('q.dateInitial > :now')
    	->setParameter('now',date('Y-m-d'))
    	->getQuery()
    	->getResult();
    }
    
    public function getStatsByEspace($espace_id){
    	$queryBuilder = $this->createQueryBuilder('a')
	    	->select('a.etatCourant , COUNT(distinct(a.id)) total, st.libelle')
	    	->innerJoin('OrangeMainBundle:Statut', 'st', 'WITH', 'a.etatCourant = st.code')
	    	->innerJoin('a.instance', 'i')
	    	->innerJoin('i.espace', 'e')
	    	->where('e.id =:id')->setParameter('id', $espace_id)
	    	->andWhere('a.etatCourant = a.etatCourant')
	    	->andWhere('st.display=:display')->setParameter('display', 1);
    	return $queryBuilder->groupBy('a.etatCourant');
    }

    /**
     *Recuperer les stats generales des instances en params
     * @param unknown $role
     */
    public function getStatsGeneralByRole($role){
        $data=array();
     	if($role===Utilisateur::ROLE_ADMIN){
    		$queryBuilder=$this->adminQueryBuilder($data);
     	}elseif ($role==Utilisateur::ROLE_ANIMATEUR)
    		$queryBuilder=$this->animateurQueryBuilder($data);
    	elseif ($role===Utilisateur::ROLE_MANAGER)
    	    $queryBuilder=$this->managerQueryBuilder($data);
    	elseif($role=== Utilisateur::ROLE_PORTEUR)
    		$queryBuilder=$this->porteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_RAPPORTEUR)
    		$queryBuilder=$this->rapporteurQueryBuilder($data);
    	elseif($role===Utilisateur::ROLE_CONTRIBUTEUR){
    		$queryBuilder=$this->createQueryBuilder('a')
    						   ->innerJoin('a.contributeur', 'c');
    		$queryBuilder=$this->filterByProfile($queryBuilder, 'c', Utilisateur::ROLE_CONTRIBUTEUR);
    		
    	}
    	$alias=$queryBuilder->getRootAlias();
    	$queryBuilder
					    	->leftJoin($alias.'.actionCyclique', 'acl1')
					    	->leftJoin('acl1.tache', 't1')
					    	->leftJoin($alias.'.instance', 'i')
					    	->select('count('.$alias.'.id) total ,'.$alias.'.etatCourant action_etat, t1.etatCourant tache_etat')
					    	->groupBy($alias.'.etatCourant')->addGroupBy('t1.etatCourant')
    						;
    	$data= $queryBuilder->getQuery()->getArrayResult();
    	$data=$this->combineTacheAndAction($data);
    	return $data;
    }
    
    public function combineTacheAndAction($data){
    	$arrData=array();
    	$i=0;
    	if(count($data)>0)
    		foreach($data as $key =>$value){
    			if (count($arrData)<=0){
    				$arrData[$i]=array( 'total'=>intval($value['total']));
    				if ($value['action_etat']!=null)
    					$arrData[$i]['etatCourant']=$value['action_etat'];
    			}else{
    				$aide=false;
    				for ($j=0; $j<count($arrData);$j++){
    					if ($value['action_etat']!=null){
    						if ($arrData[$j]['etatCourant']==$value['action_etat']){
    							$arrData[$j]['total']+=intval($value['total']);
    							$aide=true;
    							break;
    						}
    					}
    				}
    				if($aide==false){
    					$i++;
    					$arrData[$i]=array( 'total'=>intval($value['total']));
    					if ($value['action_etat']!=null)
    						$arrData[$i]['etatCourant']=$value['action_etat'];
    				}
    					
    			}
    	}
    	return $arrData;
    }
    
}
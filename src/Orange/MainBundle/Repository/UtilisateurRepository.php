<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Statut;

class UtilisateurRepository extends BaseRepository {
	
	public function getAll() {
	return parent::findAll();
	}
	

	public function getUsers($ids){
		return $this->createQueryBuilder('u')
		->where('u.id IN (:ids)')
		->setParameters(array('ids' => $ids))
		->getQuery()
		->getResult();
	}
	public function allUsers(){
		return $this->createQueryBuilder('u')
		->select('u.id')
		->getQuery()
		->getResult();
	}
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter()->andWhere('u.enabled=:enabled')
					->setParameter('enabled', true)
					->getQuery()->execute();
	}
	
	public function getEmail($id){
		return $this->createQueryBuilder('u')
		->select('u.email')
		->where('u.id = '.$id)
		->getQuery()
		->getResult();
	}
	public function listAllElements($criteria){
		//$user = $this->_user;
		$queryBuilder = $this->filter();
		$structure = $criteria->getStructure();
		if(isset($structure)) {
			$queryBuilder->innerJoin('u.structure', 's')
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
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('u');
		$queryBuilder->where($queryBuilder->expr()->in('u.id', $this->superAdminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->adminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->animateurQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->chefProjetQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->managerQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->porteurQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('u.id', $this->sourceQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		$queryBuilder->setParameters($parameters);
		return $queryBuilder;
	}
	
	public function getNextId() {
		$data = $this->createQueryBuilder('u')
		->select('MAX(u.id) as maxi')
		->getQuery()->getArrayResult();
		return (int)$data[0]['maxi'] + 1;
	}
	
	public function getArrayUtilisateur(){
		$utilisateurs= $this->filter()->select('u.id')->getQuery()->getArrayResult();
		
		$tabUtilisateur=array();
		$i=0;
		foreach ($utilisateurs as $ta){
			$tabUtilisateur[$i]=$ta['id'];
			$i++;
		}
		return $tabUtilisateur;
	}
	
	
/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('u1')->select('u1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder =  parent::createQueryBuilder('u2')->select('u2.id')
							 ->leftJoin('u2.structure', 's2')
							 ->leftJoin('OrangeMainBundle:Structure', 'st1','WITH','1=1')
							 ->leftJoin('st1.buPrincipal', 'bu1')
							 ->leftJoin('bu1.structure', 'st2')
							 ->andWhere('bu1.id = :buId')
							 ->andWhere('s2.id = st1.id OR (s2.root = st2.root AND s2.lvl >= st2.lvl AND s2.lft >= st2.lft AND s2.rgt <= st2.rgt)')
							 ->setParameter('buId', $this->_user->getStructure()->getBuPrincipal()->getId())
							;
		$data = array_merge($this->filterByProfile($queryBuilder, 'bu1', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('u3')->select('u3.id')
							 ->leftJoin('u3.structure', 's3')
							 ->leftJoin('OrangeMainBundle:Structure', 'st3','WITH','1=1')
							 ->leftJoin('st3.instance', 'i3')
							 ->andWhere('s3.root = st3.root AND s3.lvl >= st3.lvl AND s3.lft >= st3.lft AND s3.rgt <= st3.rgt')
						 ;
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array(), $direct = false) {
		$data = $data ? $data : array();
 		$queryBuilder = $this->createQueryBuilder('u4')->select('u4.id')
			->innerJoin('u4.structure', 's4');
		if($direct) {
			$queryBuilder->andWhere('u4.id IN (:collaboratorIds)')->setParameter('collaboratorIds', $this->_user->getCollaboratorsId());
		} else {
			$this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER);
		}
		$data = array_merge($queryBuilder->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('u5')->select('u5.id')
			->innerJoin('u5.sources', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('u6')->select('u6.id')
							->innerJoin('u6.projets', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}

	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('u7')->select('u7.id')
							->innerJoin('u7.action', 'a7')
							->innerJoin('a7.porteur', 'ut7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'ut7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	public function getAllPorteur(){
		return $this->createQueryBuilder('u')->select('u.id')
					->innerJoin('u.action', 'a')
					->innerJoin('a.porteur', 'ut')
					->distinct()
					->getQuery()->getArrayResult();
	}

	public function getAllCotributeur(){
		return $this->createQueryBuilder('u')->select('u.id')
					->innerJoin('u.action', 'a')
					->innerJoin('a.porteur', 'ut')
					->distinct()
					->getQuery()->getArrayResult();
	}
public function getUtilisateurByStructure($structures, $bu=null){
		if ($this->_user){
			$bu=$this->_user->getStructure()->getBuPrincipal();
		}
		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
		$structures_ids=array();
		foreach($structures as $str)
			$structures_ids[]=$str['id'];
		$queryBuilder=$rep->createQueryBuilder('s')
						->select('COUNT(distinct(u1.id)) usr, s.id, s.libelle')
						->add('from', 'OrangeMainBundle:Utilisateur u1', true)
						->innerJoin('u1.structure','s1')
						->innerJoin('s1.buPrincipal','b1')
						->andWhere('s.id in(:structs)')->setParameter('structs', $structures_ids)
						->andWhere('s1.lvl >= s.lvl ')
						->andWhere('s1.root = s.root')
						->andWhere('s1.lft  >= s.lft')
						->andWhere('s1.rgt <= s.rgt')
						->andWhere('b1=:bu')
				        ->setParameter('bu', $bu)
						->groupBy('s.id');
		return $queryBuilder;
	}
	public function getUtilisateurActifByStructure($structures, $bu=null){
		if ($this->_user){
			$bu=$this->_user->getStructure()->getBuPrincipal();
		}
		$rep=$this->_em->getRepository('OrangeMainBundle:Structure');
		$structures_ids=array();
		foreach($structures as $str)
			$structures_ids[]=$str['id'];
		$queryBuilder=$rep->createQueryBuilder('s')
							->select('COUNT(distinct(u1.id)) usr, s.id, s.libelle')
							->add('from', 'OrangeMainBundle:Utilisateur u1', true)
							->innerJoin('u1.structure','s1')
							->innerJoin('s1.buPrincipal','b1')
							->innerJoin('u1.action', 'a')
							->innerJoin('a.actionStatut', 'ahs')
							->innerJoin('ahs.statut', 'st')
							->where('st.code=:code') ->setParameter('code', Statut::ACTION_NON_ECHUE)
							->andWhere('s.id in(:structs)')->setParameter('structs', $structures_ids)
							->andWhere('s1.lvl >= s.lvl ')
							->andWhere('s1.root = s.root')
							->andWhere('s1.lft  >= s.lft')
							->andWhere('s1.rgt <= s.rgt')
							->andWhere('b1=:bu')
				        	->setParameter('bu', $bu)
							->groupBy('s.id');
		return $queryBuilder;
	}
		public function listByInstance($idStructure){
			return $this->createQueryBuilder('u')
						->innerJoin('u.structure', 's')
						->add('from', 'OrangeMainBundle:Utilisateur u1', true)
						->select('u1')
						->innerJoin('u1.structure', 's1')
						->where('s.id IN (:id)')
						->andWhere('s1.lvl >= s.lvl')
						->andWhere('s1.root = s.root')
						->andWhere('s1.lft >= s.lft')
						->andWhere('s1.rgt <= s.rgt')
						->setParameter('id', $idStructure)
						->distinct()
						//->getQuery()->getArrayResult()
			;
		}
		
	
		public function getMailsByBu(){
			return $this->createQueryBuilder('u')
						->select('u.id,u.email')
						->innerJoin('u.structure', 's')
						->where('s.buPrincipal=:bu')->setParameter('bu', $this->_user->getStructure()->getBuPrincipal())
						->getQuery()->getArrayResult();
			
		}
		
		public function getMembreEspace($espace_id){
			return $this->createQueryBuilder('u')
			->innerJoin('u.membreEspace', 'me')
			->innerJoin('me.espace', 'e')
			->where('e.id=:id')->setParameter('id', $espace_id);
		}
		
		/**
		 * @param \Orange\MainBundle\Entity\Utilisateur $manager
		 */
		public function getMembreCollaborateur($manager){
			return $this->createQueryBuilder('u')
				->where('u.id IN (:ids)')->setParameter('ids', $manager->getCollaboratorsId());
		}
		
		public function getAllDestinataireOfReporting(){
			return $this->createQueryBuilder('u')
						->select('partial u.{id , email}')
			            ->innerJoin('u.reporting','r')->getQuery()->execute();
		}
}


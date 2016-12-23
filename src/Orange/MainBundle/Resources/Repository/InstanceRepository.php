<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Entity\TypeInstance;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\Statut;

class InstanceRepository extends BaseRepository{
	
	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter()->getQuery ()->execute();
	}
	
	public function filtrer() {
		$queryBuilder = $this->createQueryBuilder('i');
		if($this->_user->hasRole(Utilisateur::ROLE_SUPER_ADMIN)) {
			$queryBuilder->where('1=1');
		}
		if($this->_user->hasRole(Utilisateur::ROLE_ADMIN)) {
			$bu = $this->_user->getStructure()->getBuPrincipal()->getId();
			$queryBuilder->innerJoin('i.bu', 'bu')->where('bu.id='.$bu);
		}
		return $queryBuilder;
	}
	public function filter() {
					$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('i');
		$queryBuilder->where($queryBuilder->expr()->in('i.id', $this->superAdminQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->adminQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->animateurQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->chefProjetQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->managerQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->porteurQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->sourceQueryBuilder($data)->getDQL()))
					->orWhere($queryBuilder->expr()->in('i.id', $this->rapporteurQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	}
	
	public function filterForSignalisation() {
		$queryBuilder = $this->filter();
		$queryBuilder->innerJoin('i.bu', 'b9')
			->andWhere('i.typeInstance = :typeInstance')->setParameter('typeInstance', 2)
			->andWhere('b9.id = :bu9')->setParameter('bu9', $this->_user->getStructure()->getBuPrincipal()->getId());
		return $queryBuilder;
	}
	
	/**
	 * Methode utilise pour charger la liste des actions
	 * @param unknown $criteria
	 * @param unknown $porteur
	 */
	public function listAllElements() {
		$queryBuilder = $this->filtrer()
			->select('partial i.{id, libelle, description},
					partial ty.{id, libelle},
					partial dom.{id, libelleDomaine},
					partial ta.{id,type}')
			->leftJoin('i.typeInstance', 'ty')
			->leftJoin('i.domaine', 'dom')
			->leftJoin('i.typeAction', 'ta');
		//->addGroupBy('i.id');
		return $queryBuilder;
	}
	public function getIds() {
		$ids = array();
		$data = $this->createQueryBuilder('i')->select('i.id')->getQuery()->getResult();
		foreach($data as $value) {
			array_push($ids, $value['id']);
		}
		return $ids;
	}

		/**
		 * @return QueryBuilder
		 */
		public function superAdminQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i1')->select('i1.id');
			$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function adminQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i2')
								 ->innerJoin('i2.bu', 'b2');
			$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function animateurQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i3')->select('i3.id');
			$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function managerQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i4')->select('i4.id')
															->innerJoin('i4.action', 'a4')
															->innerJoin('a4.porteur', 'ut4')
															->innerJoin('ut4.structure', 's4');
			$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function sourceQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i5')->select('i5.id')
								->innerJoin('i5.sourceInstance', 'so5');
			$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function chefProjetQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i6')->select('i6.id')
			->innerJoin('i6.chantier', 'c6')
			->innerJoin('c6.projet', 'p6');
			$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		/**
		 * @return QueryBuilder
		 */
		public function porteurQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i7')->select('i7.id')
															->innerJoin('i7.action', 'a7')
															->innerJoin('a7.porteur', 'ut7');
			$data = array_merge($this->filterByProfile($queryBuilder, 'ut7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}

		public function rapporteurQueryBuilder(&$data = array()) {
			$queryBuilder = $this->createQueryBuilder('i8')->select('i8.id')
								           ->innerJoin('i8.action', 'a8')
								           ->innerJoin('a8.porteur', 'u8')
								           ->innerJoin('u8.structure', 's8')
								           ->distinct()
			;
			$data = array_merge($this->filterByProfile($queryBuilder, 's8', Utilisateur::ROLE_RAPPORTEUR)->getParameters()->toArray(), $data);
			return $queryBuilder;
		}
		
		
		public function getArrayInstance(){
			$instances= $this->filter()->select('i.id')->getQuery()->getArrayResult();
			$tabInstances=array();
			$i=0;
			foreach ($instances as $inst){
				$tabInstances[$i]=$inst['id'];
				$i++;
			}
			return $tabInstances;
		}
		public function getInstanceRapporteur($structures) {
					$structureIds=array();
					foreach ($structures as $str){
						$structureIds[]=$str['id'];
					}
					$queryBuilder = $this->createQueryBuilder('i')
										           ->innerJoin('i.structure', 's')
												   ->where('s.id IN(:structureIds)')
												   ->setParameter('structureIds', $structureIds);
					$data = $queryBuilder->getQuery()->getArrayResult();
					return $data;
				}
		public function getInstanceSignaletiqueAnimateur(){
			$queryBuilder=$this->createQueryBuilder('i')
					   ->innerJoin('i.typeInstance', 't')
					   ->where('t.code=:code')->setParameter('code', TypeInstance::INSTANCE_ACTION_SIGNALETIQUE)
			;
			$data=$this->filterByProfile($queryBuilder,'i',Utilisateur::ROLE_ANIMATEUR);
			return $data->getQuery()->getArrayResult();
		}
		
		public function getInstancesAdmin() {
			exit('ok');
			$bu=$this->_user->getStructure()->getBuPrincipal()->getId();
			$queryBuilder = $this->createQueryBuilder('i')
			->innerJoin('i.action', 'a')
			->innerJoin('a.porteur', 'u')
			->innerJoin('u.structure', 's')
			->innerJoin('s.buPrincipal', 'b')
			->where('b.id=:buP')
			->setParameter('buP', $bu)
			->distinct();
			return $queryBuilder->getQuery()->getArrayResult();
		}
		
		
		public function getInstancesEnConributions($user_id){
			$codesArchives=array(Statut::ABANDONNEE_ARCHIVEE, Statut::SOLDEE_ARCHIVEE);
			return  $this->createQueryBuilder('i')
						->innerJoin('i.action', 'a')
						->innerJoin('a.contributeur', 'c')
						->innerJoin('c.utilisateur', 'u')
						->where('u.id=:user_id')
						->setParameter('user_id', $user_id)
						->andWhere('a.etatCourant NOT IN (:code)')
						->setParameter('code', $codesArchives)
						->distinct()
			->getQuery()->getArrayResult();
		}
		
		public function getInstanceByStructure($structure_id){
			$structure=$this->_em->getRepository('OrangeMainBundle:Structure')->find($structure_id);
			$queryBuilder=$this->createQueryBuilder('i')
								->innerJoin('i.action', 'a')
								->innerJoin('a.porteur', 'u')
								->innerJoin('a.actionStatut', 'ahs')
								->innerJoin('ahs.statut', 'st')
								->innerJoin('u.structure', 's')
								->andWhere('s.lvl >= :lvl')
								->andWhere('s.root = :root')
								->andWhere('s.lft  >= :lft')
								->andWhere('s.rgt <= :rgt')
								->andWhere('st.code=:code')
								->setParameter('code', Statut::ACTION_TRAITEMENT)
								->setParameter('lvl', $structure->getLvl())
								->setParameter('root', $structure->getRoot())
								->setParameter('lft', $structure->getLft())
								->setParameter('rgt', $structure->getRgt())->distinct();
			return $queryBuilder;
		}
	
		public function getInstanceByRole($role){
			$data=array();
			if ($role==Utilisateur::ROLE_ADMIN)
				$instances=$this->adminQueryBuilder($data)->select('i2')->leftJoin('i2.parent', 'p')->andWhere('p.id IS NULL');
			elseif($role==Utilisateur::ROLE_ANIMATEUR)
				$instances=$this->animateurQueryBuilder($data)->select('i3');
			elseif ($role==Utilisateur::ROLE_MANAGER)
				$instances=$this->managerQueryBuilder($data)->select('i4');
			elseif($role==Utilisateur::ROLE_RAPPORTEUR)
				$instances=$this->rapporteurQueryBuilder($data)->select('i8');
			elseif($role==Utilisateur::ROLE_PORTEUR)
				$instances=$this->porteurQueryBuilder($data)->select('i7');
			else
				$instances=$this->filter();
			return $instances;
		}
}
	
	

?>
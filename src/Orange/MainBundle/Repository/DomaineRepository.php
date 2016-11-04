<?php 

namespace Orange\MainBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Repository\EntityRepository;
use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\CustomInterface\RepositoryInterface;

class DomaineRepository extends BaseRepository{

	
	public function findAll() {
		// TODO: Auto-generated method stub
		return $this->filter ()->getQuery ()->execute ();
	}

	public  function listQueryBuilder(){
		return $this->filter();
		
	}
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = $this->createQueryBuilder('d');
		$queryBuilder->where($queryBuilder->expr()->in('d.id', $this->superAdminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->adminQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->animateurQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->chefProjetQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->managerQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->porteurQueryBuilder($data)->getDQL()))
		->orWhere($queryBuilder->expr()->in('d.id', $this->sourceQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		
		return $queryBuilder->setParameters($parameters);
	
	}
	/**
	 * @return QueryBuilder
	 */
	public function superAdminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d1')->select('d1.id');
		$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function adminQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d2')->select('d2.id')
		->innerJoin('d2.bu', 'b2');
		$data = array_merge($this->filterByProfile($queryBuilder, 'b2', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function animateurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d3')->select('d3.id')
		->innerJoin('d3.instance', 'i3');
		$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function managerQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d4')->select('d4.id')
		->innerJoin('d4.bu', 'b4')
		->innerJoin('b4.structureBuPrincipal', 's4');
		$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function sourceQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d5')->select('d5.id')
		->innerJoin('d5.instance', 'i5')
		->innerJoin('i5.sourceInstance', 'so5');
		$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function chefProjetQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d6')->select('d6.id')
		->innerJoin('d6.projet', 'p6');
		$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function porteurQueryBuilder(&$data = array()) {
		$queryBuilder = $this->createQueryBuilder('d7')->select('d7.id')
		->innerJoin('d7.action', 'a7')
		->innerJoin('a7.porteur', 'ut7');
		$data = array_merge($this->filterByProfile($queryBuilder, 'ut7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
		return $queryBuilder;
	}
	
	/**
	 * @return QueryBuilder
	 */
	public function getDomainesByEspace($espace_id) {
		$queryBuilder = $this->createQueryBuilder('d')
		->innerJoin('d.instance', 'i')
		->innerJoin('i.espace', 'e')
		->where('e.id=:id')->setParameter('id', $espace_id);
		return $queryBuilder;
	}
	public function getArrayDomaine(){
		$domaines= $this->filter()->select('d.id')->getQuery()->getArrayResult();
		$tabDomaines=array();
		$i=0;
		foreach ($domaines as $ta){
			$tabDomaines[$i]=$ta['id'];
			$i++;
		}
		return $tabDomaines;
	}
	public function listDomaineByInstance($id, $libelle){
		return $this->createQueryBuilder('d')
		->innerJoin('d.instance', 'i')
		->where('i.id = :id AND d.libelleDomaine LIKE :libelle')
		->setParameter('id', $id)
		->setParameter('libelle', '%\_'.$libelle.'\_%')
		->distinct()
		->getQuery()->getArrayResult();
	}
	public function listByInstance($id) {
		return $this->createQueryBuilder('d')
		->innerJoin('d.instance', 'i')
		->where('i.id = :id')
		->setParameter('id', $id)
		->getQuery()->getArrayResult();
	}
	
}

?>
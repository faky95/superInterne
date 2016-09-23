<?php 
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Doctrine\ORM\QueryBuilder;
use Orange\MainBundle\Entity\TypeStructure;
use Orange\MainBundle\Entity\Structure;
use Orange\MainBundle\Entity\Statut;

class StructureRepository extends BaseRepository {
	public function idsArch(){
		return $this->createQueryBuilder('s')
		->select('s.id')
		->innerJoin('s.architectureStructure', 'a')
		->getQuery()
		->getArrayResult();
	}
	public function StructureToUpdate(){
		$ids = $this->idsArch();
		return $this->createQueryBuilder('s')
		->where('s.id NOT IN (:ids)')->setParameter('ids', $ids)
		->getQuery()
		->getResult();
	}
	public function getIdStructure($id) {
		$array = array();
		$query = parent::createQueryBuilder('s')
					->select('s.id')
					->innerJoin('s.parent', 'p')
					->where('p.id = :id')
					->setParameter('id', $id)
			->getQuery()->getArrayResult();
		foreach ($query as $id){
			array_push($array, $id['id']);
		}
		return $array;
	}
	
	public function listAllElements($criteria = array()) {
		$queryBuilder = $this->filter();
		$parent = $criteria->getParent();
		$type = $criteria->getTypeStructure();
		if(isset($type)) {
			$queryBuilder->andWhere('s.typeStructure = :typeStructure')
			->setParameter('typeStructure', $type);
		}
		if(isset($parent)) {
			$queryBuilder->andWhere('s.root = :root')->andWhere('s.lvl >= :lvl')->andWhere('s.lft >= :lft')->andWhere('s.rgt <= :rgt')
				->setParameter('root', $parent->getRoot())->setParameter('lvl', $parent->getLvl())
				->setParameter('lft', $parent->getLft())->setParameter('rgt', $parent->getRgt());
		}
		return $queryBuilder;
	}
	
	public function filter() {
		$data = array();$parameters = array();
		$queryBuilder = parent::createQueryBuilder('s');
		$queryBuilder->where($queryBuilder->expr()->in('s.id', $this->superAdminQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->adminQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->animateurQueryBuilder($data)->getDQL()))
// 						->orWhere($queryBuilder->expr()->in('s.id', $this->chefProjetQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->managerQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->porteurQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->sourceQueryBuilder($data)->getDQL()))
						->orWhere($queryBuilder->expr()->in('s.id', $this->rapporteurQueryBuilder($data)->getDQL()));
		foreach($data as $value) {
			$parameters[$value->getName()] = $value->getValue();
		}
		return $queryBuilder->orderBy('s.lft', 'ASC')->setParameters($parameters);
	
	}
	
	public function getNextId() {
		$data = parent::createQueryBuilder('s')
		->select('MAX(s.id) as maxi')
		->getQuery()->getArrayResult();
		return (int)$data[0]['maxi'] + 1;
	}
	
	/**
	 * Methode pour les imports
	 * @param array $data
	 * @param \Orange\MainBundle\Query\BaseQuery $query
	 * @param integer $line
	 */
	public function saveLine($data = array(), $query, $buP ,$line) {
		$structure = new Structure();
		if(trim($data[3])){
			$buP=$this->_em->getRepository('OrangeMainBundle:Bu')->findOneByLibelle($query->trim($data[3]));
		} else {
			$buP = null;
			throw new \Exception(sprintf("La direction à la ligne %s n'existe pas", $line));
		}
		if(strlen(trim($data[1]))>0) {
			$parent = $this->findOneBy(array('libelle'=>trim($data[1]), 'buPrincipal' => $buP->getId()));
			if(!$parent) {
				throw new \Exception(sprintf("La structure parente à la ligne %s n'existe pas", $line));
			}else if($this->findOneBy(array('libelle'=>trim($data[0]), 'buPrincipal' => $buP))){
				throw new \Exception(sprintf("La structure à la ligne %s existe deja", $line));
			}
		} else {
			$parent = null;
		}
		if(trim($data[2])){
			$typeS=$this->_em->getRepository('OrangeMainBundle:TypeStructure')->findOneByCode(strtoupper($query->trim($data[2])));
		} else {
			$typeS = null;
			throw new \Exception(sprintf("Le type à la ligne %s n'existe pas", $line));
		}
		
		$structure->setLibelle($data[0]);
		$structure->setParent($parent);
		$structure->setTypeStructure($typeS);
		$structure->setBuPrincipal($buP);
		$this->getEntityManager()->persist($structure);
		$this->getEntityManager()->flush();
	}
	
    public function getDirectionAndDepartmentByBu($buP){
    	$queryBuilder =  parent::createQueryBuilder ('s') ;
    	return $queryBuilder->andWhere('s.buPrincipal=:buP')->andWhere('s.lvl <= :lvl')
    			->setParameter('buP', $buP)->setParameter('lvl', 1)
    			->getQuery()->execute();
    }	
    
    /**
     * @return QueryBuilder
     */
    public function superAdminQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s1')->select('s1.id');
    	$data = array_merge($this->filterByProfile($queryBuilder, null, Utilisateur::ROLE_SUPER_ADMIN)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function adminQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s2')->select('s2.id')
    					        ->add('from', 'OrangeMainBundle:Structure st1', true)
								->leftJoin('st1.buPrincipal', 'bu1')
								->leftJoin('bu1.structure', 'st2')
								->andWhere('bu1.id = :buId')
								->andWhere('s2.id = st1.id OR (s2.root = st2.root AND s2.lvl >= st2.lvl AND s2.lft >= st2.lft AND s2.rgt <= st2.rgt)')
								->setParameter('buId', $this->_user->getStructure()->getBuPrincipal()->getId());
    	$data = array_merge($this->filterByProfile($queryBuilder, 'bu1', Utilisateur::ROLE_ADMIN)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function animateurQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s3')->select('s3.id')
    						 ->innerJoin('s3.utilisateurs', 'u3')
    						 ->innerJoin('u3.action', 'a3')
    						 ->innerJoin('a3.instance', 'i3');
    	$data = array_merge($this->filterByProfile($queryBuilder, 'i3', Utilisateur::ROLE_ANIMATEUR)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function managerQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s4')->select('s4.id');
    	$data = array_merge($this->filterByProfile($queryBuilder, 's4', Utilisateur::ROLE_MANAGER)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function sourceQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s5')->select('s5.id')
					    	 ->innerJoin('s5.instance', 'i5')
					    	 ->innerJoin('i5.sourceInstance', 'so5');
    	$data = array_merge($this->filterByProfile($queryBuilder, 'so5', Utilisateur::ROLE_SOURCE)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function chefProjetQueryBuilder(&$data = array()) {
//     	$queryBuilder = parent::createQueryBuilder('s6')->select('s6.id')
// 					    	->innerJoin('a6.porteur', 'u6')
// 					    	->innerJoin('a6.instance', 'i6')
// 					    	->innerJoin('i6.chantier', 'c6')
// 					    	->innerJoin('c6.projet', 'p6');
//     	$data = array_merge($this->filterByProfile($queryBuilder, 'p6', Utilisateur::ROLE_CHEF_PROJET)->getParameters()->toArray(), $data);
//     	return $queryBuilder;
    }
    
    /**
     * @return QueryBuilder
     */
    public function porteurQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s7')->select('s7.id')
					    	 ->innerJoin('s7.instance', 'i7')
					    	 ->innerJoin('i7.action', 'a7')
					    	 ->innerJoin('a7.porteur', 'u7');
    	$data = array_merge($this->filterByProfile($queryBuilder, 'u7', Utilisateur::ROLE_PORTEUR)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
   
    /**
     * @return QueryBuilder
     */
    public function rapporteurQueryBuilder(&$data = array()) {
    	$queryBuilder = parent::createQueryBuilder('s8')->select('s8.id');
    	$data = array_merge($this->filterByProfile($queryBuilder, 's8', Utilisateur::ROLE_RAPPORTEUR)->getParameters()->toArray(), $data);
    	return $queryBuilder;
    }
    
    
    
/**
     * @return QueryBuilder
     */
    public function getDirectionsByBu() {
    $queryBuilder =  parent::createQueryBuilder ('st') ->select('st.id , st.libelle, b.libelle bu')
    					  ->innerJoin('st.buPrincipal', 'b');
					$queryBuilder
					->add('from', 'OrangeMainBundle:Structure st1', true)
					->innerJoin('st1.buPrincipal', 'b1')
					->leftJoin('b1.structure', 'st2')
					->andWhere('st1 = st OR st2 = st')
					->andWhere('st1.lvl<=:val')
					->setParameter('val', 1)
					->distinct()
					;
    	return $this->filterByProfile($queryBuilder, 'b1', Utilisateur::ROLE_ADMIN);
    }
    /**
     * Recupere les structures et les structures directement lie
     */
	 public function getStructureAndStructureDirecteByStructure($structure){
	    	$str = $this->find($structure);
	    	$queryBuilder =  parent::createQueryBuilder('s')->select('s') ;
	    	return $queryBuilder->where('s.id=:structure ')->setParameter('structure', $structure)
				    	->orWhere('s.lvl = :lvl and s.parent=:parent ')
				    	->setParameter('lvl', $str->getLvl()+1)
				    	->setParameter('parent', $str)
				    	->orderBy('s.lvl');
	    }
    
    public function listAllStructures($id) {
    	return parent::createQueryBuilder('s')
    	->innerJoin('s.instance', 'i')
    	->where('i.id = :instance')
    	->setParameter('instance', $id)
    	->getQuery()
    	->execute();
    }
    
    public function getChildrenByParent($id){
    	return parent::createQueryBuilder('s')
    	->select('s1')
    	->add('from', 'OrangeMainBundle:Structure s1', true)
    	->where('s.id = :id')
    	->andWhere('s.lvl <= s1.lvl')
    	->andWhere('s.root = s1.root')
    	->andWhere('s.lft  <= s1.lft')
    	->andWhere('s.rgt >= s1.rgt')
    	->setParameter('id', $id)
    	;
    }
    public function getDirectionAndPoleByBu($bu){
    	$queryBuilder =  parent::createQueryBuilder ('s')
    	->select('s.id , s.libelle')
    	->innerJoin('s.buPrincipal', 'b')
    	->innerJoin('s.typeStructure', 't')
    	->where('b=:bu')->setParameter('bu', $bu)
    	->andWhere('t.code=:typeD')->setParameter('typeD', TypeStructure::NIVEAU_DIRECTION)
    	->orWhere('t.code=:typeP')->setParameter('typeP', TypeStructure::NIVEAU_POLE)
    	;
    	return $queryBuilder;
    }

    public function getStructureByRole($role){
    	$data=array();
    	$states=array(Statut::ABANDONNEE_ARCHIVEE, Statut::SOLDEE_ARCHIVEE);
	    if($role==Utilisateur::ROLE_ADMIN) {
	    		$structures = $this->adminQueryBuilder($data)->select('s2');
	    } elseif($role==Utilisateur::ROLE_ANIMATEUR) {
				$structures=$this->animateurQueryBuilder($data)
								 ->select('s3')
								 ->andWhere('a3.etatCourant not in(:etat)')
								 ->setParameter('etat', $states)
								 ->distinct()
								 ;
    	} elseif($role==Utilisateur::ROLE_MANAGER) {
				$structures=$this->managerQueryBuilder($data)->select('s4');
    	} elseif($role==Utilisateur::ROLE_RAPPORTEUR) {
	    		$structures = $this->rapporteurQueryBuilder($data)->select('s8');
    	} else {
	    	 $structures = $this->filter();
	    }
	    $alias=$structures->getRootAlias();
	    $structures->orderBy($alias.'.lvl');
	    return $structures;
    }
    
    /**
     * 
     * @param unknown $structures
     */
	public function getChildren($structures){
		$structuresIds=array();
		foreach($structures as $structure) 
			$structuresIds[] = $structure->getId();
			$queryBuilder =  parent::createQueryBuilder('s');
			$queryBuilder
			->select('s1')
			->add('from', 'OrangeMainBundle:Structure s1', true)
			->where('s.id in(:structures) ')->setParameter('structures', $structuresIds)
			->andWhere('s.lvl <= s1.lvl')
    		->andWhere('s.root = s1.root')
    		->andWhere('s.lft  <= s1.lft')
    		->andWhere('s.rgt >= s1.rgt')
    		->orderBy('s1.lvl')
			;
			return $queryBuilder;
	}
}

?>
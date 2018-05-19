<?php
namespace Orange\MainBundle\Repository;

use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Instance;
use Orange\MainBundle\Entity\Synthese;

class SyntheseRepository extends BaseRepository {
	
	/**
	 * @param unknown $user        	
	 */
	public function reportingByInstance($role, $criteria) {
		$queryBuilder = $this->createQueryBuilder('q');
		$queryBuilder->select("i.id, i.libelle, i.couleur, u.id user_id, CONCAT(u.prenom, ' ', u.nom) porteur")
			->leftJoin('q.instance', 'i')
			->innerJoin('q.structure', 's')
			->innerJoin('q.utilisateur', 'u')
			->andWhere('IDENTITY(q.type) = 1');
		foreach(Synthese::$fields as $field) {
			$queryBuilder->addSelect(sprintf('SUM(q.%s) as %s', $field, $field));
		}
		foreach(array_keys(Synthese::$formules) as $func) {
			$queryBuilder->addSelect(sprintf('(CASE WHEN SUM(q.total)=0 THEN AVG(q.%s) ELSE SUM(IFNULL(q.%s, 0))/COUNT(q.%s) END) as %s', $func, $func, $func, $func, $func, $func));
		}
		if($role == Utilisateur::ROLE_ADMIN) {
			$queryBuilder->andWhere('i.id IN (:instanceIds)')->setParameter('instanceIds', $this->_user->getStructure()->getBuPrincipal()->getInstanceIds());
		} elseif($role == Utilisateur::ROLE_ANIMATEUR) {
			$queryBuilder->andWhere('i.id IN (:instanceIds)')->setParameter('instanceIds', $this->_user->getInstanceIds());
		} elseif($role === Utilisateur::ROLE_MANAGER || $role === Utilisateur::ROLE_RAPPORTEUR) {
			$queryBuilder->innerJoin('OrangeMainBundle:Structure', 's1', 'WITH', 's.lvl >= s1.lvl and s.root = s1.root and s.lft  >= s1.lft and s.rgt <= s1.rgt');
			if($role === Utilisateur::ROLE_MANAGER) {
				$queryBuilder->andWhere('s = :structure')->andWhere('u! = :userId')
				 ->setParameter('userId', $this->_user->getId())->setParameter('structure', $this->_user->getStructure());
			} else {
				$queryBuilder->andWhere('s.id IN (:structureIds)')->setParameter('structureIds', $this->_user->getStructureIdsForRapporteur());
			}
		} elseif($role === Utilisateur::ROLE_PORTEUR) {
			$queryBuilder->andWhere('u.id = :userId')->andWhere('IDENTITY(q.type) = 1')->setParameter('userId', $this->_user->getId());
		} elseif($role === Utilisateur::ROLE_CONTRIBUTEUR) {
			$queryBuilder->andWhere('u.id = :userId')->andWhere('IDENTITY(q.type) = 2')->setParameter('userId', $this->_user->getId());
		} else {
			$queryBuilder = $this->filter();
		}
		$this->filtres($queryBuilder, $criteria, 'q');
		return $queryBuilder->groupBy('i.id');
	}
	
	/**
	 * @param unknown $user
	 */
	public function reportingByInstanceAndPorteur($role, $criteria) {
		return $this->reportingByInstance($role, $criteria)->addGroupBy('u.id');
	}
	
	/**
	 * Recuperer les stats des structures en parametre groupés statut
	 */
	public function reportingByStructure($role, $criteria, $structures) {
		$criteria =($criteria) ? $criteria : new \Orange\MainBundle\Entity\Action();
		//$structures = null;
		$instances = null;
		$data = array();
		//$user = $this->_user;
		$rep = $this->_em->getRepository('OrangeMainBundle:Structure');
		if($role === Utilisateur::ROLE_ADMIN) {
			//$structures = $rep->adminQueryBuilder($data)->addSelect('s2.libelle')->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_ANIMATEUR) {
			$instances = $this->_em->getRepository('OrangeMainBundle:Instance')->getInstanceByRole(Utilisateur::ROLE_ANIMATEUR)->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_RAPPORTEUR) {
			//$structures = $rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_MANAGER) {
			//$structures = $rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
		} else {
			//$queryBuilder = $this->filter();
		}
		$queryBuilder = $rep->createQueryBuilder('s')->select('s.id, s.libelle')
			->leftJoin('OrangeMainBundle:Synthese', 'q', 'WITH', 'IDENTITY(q.type) = 1')
			->innerJoin('q.structure', 's1')->andWhere('s1.lvl >= s.lvl')->andWhere('s1.root = s.root')->andWhere('s1.lft  >= s.lft')->andWhere('s1.rgt <= s.rgt');
		foreach(Synthese::$fields as $field) {
			$queryBuilder->addSelect(sprintf('SUM(q.%s) as %s', $field, $field));
		}
		foreach(array_keys(Synthese::$formules) as $formule) {
			$queryBuilder->addSelect(sprintf('AVG(q.%s) as %s', $formule, $formule));
		}
		if($role == Utilisateur::ROLE_ANIMATEUR) {
			$instancesIds = array();
			foreach($instances as $data) {
				$instancesIds[] = \is_object($data) ? $data->getId() : $data ['id'];
			}
			$queryBuilder->andWhere('IDENTITY(q.instance) in(:insts)')->setParameter('insts', $instancesIds);
		} else {
			$structureIds = array();
			foreach($structures as $data) {
				$structureIds[] = \is_object($data) ? $data->getId() : $data ['id'];
			}
			$queryBuilder->andWhere('s.id in(:structs)')->setParameter('structs', $structureIds);
		}
		if($role == Utilisateur::ROLE_MANAGER) {
			$queryBuilder->andWhere('q.utilisateur != :me')->setParameter('me', $this->_user);
		}
		exit(implode(',', $structureIds));
		return $queryBuilder->groupBy('s.id')->orderBy('s.lvl');
	}
	
	/**
	 * Recuperer les stats des structures en parametre groupés par instance , statut
	 *
	 * @param unknown $criteria
	 * @param unknown $structures
	 */
	public function reportingByStructureAndInstance($role, $criteria) {
		$criteria =($criteria) ? $criteria : new \Orange\MainBundle\Entity\Action();
		$structures = null;
		$data = array();
		$user = $this->_user;
		$rep = $this->_em->getRepository('OrangeMainBundle:Structure');
		if($role === Utilisateur::ROLE_ADMIN) {
			$structures = $rep->getStructureAndStructureDirecteByStructure($this->_user->getStructure()->getRoot())->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_ANIMATEUR) {
			$structures = $rep->animateurQueryBuilder($data)->addSelect('s3.libelle')->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_RAPPORTEUR) {
			$structures = $rep->rapporteurQueryBuilder($data)->addSelect('s8.libelle')->getQuery()->getArrayResult();
		} elseif($role === Utilisateur::ROLE_MANAGER) {
			$structures = $rep->getStructureAndStructureDirecteByStructure($user->getStructure()->getId())->getQuery()->getArrayResult();
		}
		$structureIds = array();
		foreach($structures as $data) {
			$structureIds [] = \is_object($data) ? $data->getId() : $data ['id'];
		}
		$queryBuilder = $rep->createQueryBuilder('s')
			->select('s.libelle f_libelle, i.libelle inst, i.libelle s_libelle, s.id f_id,i.id s_id')
			->add('from', 'OrangeMainBundle:Synthese q', true)
			->innerJoin('q.structure', 's1')
			->innerJoin('q.instance', 'i')
			->innerJoin('i.bu', 'b')
			->leftJoin('i.espace', 'e')
			->andWhere('s.id in(:structs)')->setParameter('structs', $structureIds)
			->andWhere('s1.lvl >= s.lvl')
			->andWhere('s1.root = s.root')
			->andWhere('s1.lft  >= s.lft')
			->andWhere('s1.rgt <= s.rgt')
			->andWhere('b.id=s.buPrincipal')
			->andWhere(' e.id IS NULL');
		foreach(Synthese::$fields as $field) {
			$queryBuilder->addSelect(sprintf('SUM(q.%s) as %s', $field, $field));
		}
		foreach(array_keys(Synthese::$formules) as $formule) {
			$queryBuilder->addSelect(sprintf('AVG(q.%s) as %s', $formule, $formule));
		}
		$this->filtres($queryBuilder, $criteria, 'q');
		$data = $queryBuilder->groupBy('f_id')->addGroupBy('s_id')->getQuery()->getArrayResult();
		return $data;
	}
	
	/**
	 * gerer les filtres
	 */
	public function filtres($queryBuilder, $criteria, $alias) {
		$criteria =($criteria) ? $criteria : new \Orange\MainBundle\Entity\Action();
		if($criteria->getPorteur()) {
			$queryBuilder->andWhere(sprintf('%s.utilisateur = :utilisateur AND IDENTITY(%s.type) = 1', $alias, $alias))->setParameter('utilisateur', $criteria->getPorteur());
		}
		if(count($criteria->instances) > 0) {
			$instIDs = array();
			foreach($criteria->instances as $val) {
				$instIDs [] = $val->getId();
			}
			$queryBuilder->andWhere($alias.'.instance in(:instanceIds)')->setParameter('instanceIds', $instIDs);
		}
		if($criteria->getDomaine()) {
			$queryBuilder->andWhere($alias.'.domaine = :domaine')->setParameter('domaine', $criteria->getDomaine());
		}
		if($criteria->getTypeAction()) {
			$queryBuilder->andWhere($alias.'.typeAction = :type_action')->setParameter('type_action', $criteria->getTypeAction());
		}
		if($criteria->getStructure()) {
			$structure = $criteria->getStructure();
			$queryBuilder->andWhere('s.lvl >= :level')->andWhere('s.root = :root')->andWhere('s.lft >= :left')->andWhere('s.rgt <= :right')->setParameter('level', $structure->getLvl())->setParameter('root', $structure->getRoot())->setParameter('left', $structure->getLft())->setParameter('right', $structure->getRgt());
		}
	}
	
	/* ------------------------Stats ------------------------ */
	public function combineTacheAndActionComplexe($data) {
		$arrData = array();
		$i = 0;
		if(count($data) > 0)
			foreach($data as $value) {
				if(count($arrData) <= 0) {
					$arrData [$i] = array(
							's_id' => $value ['s_id'],
							's_libelle' => $value ['s_libelle'],
							'f_id' => $value ['f_id'],
							'f_libelle' => $value ['f_libelle'],
							'total' => intval($value ['total'])
						);
					if($value ['tache_etat'] == null) {
						$arrData [$i] ['etatCourant'] = $value ['action_etat'];
					} else {
							$arrData [$i] ['etatCourant'] = $value ['tache_etat'];
					}
				} else {
					$aide = false;
					for($j = 0; $j < count($arrData); $j ++) {
						if($value ['tache_etat'] == null) {
							if($arrData [$j] ['etatCourant'] == $value ['action_etat'] && $arrData [$j] ['f_id'] == $value ['f_id'] && $arrData [$j] ['s_id'] == $value ['s_id']) {
								$arrData [$j] ['total'] += intval($value ['total']);
								$aide = true;
								break;
							}
						} else {
							if($arrData [$j] ['etatCourant'] == $value ['tache_etat'] && $arrData [$j] ['f_id'] == $value ['f_id'] && $arrData [$j] ['s_id'] == $value ['s_id']) {
								$arrData [$j] ['total'] += intval($value ['total']);
								$aide = true;
								break;
							}
						}
					}
					if($aide == false) {
						$i ++;
						$arrData [$i] = array(
								's_id' => $value ['s_id'],
								's_libelle' => $value ['s_libelle'],
								'f_id' => $value ['f_id'],
								'f_libelle' => $value ['f_libelle'],
								'total' => intval($value ['total'])
						);
						if($value ['tache_etat'] == null)
							$arrData [$i] ['etatCourant'] = $value ['action_etat'];
							else
								$arrData [$i] ['etatCourant'] = $value ['tache_etat'];
					}
				}
			}
		return $arrData;
	}
	
}
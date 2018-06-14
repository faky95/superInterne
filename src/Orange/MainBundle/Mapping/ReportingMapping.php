<?php
namespace Orange\MainBundle\Mapping;

use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\Synthese;

class ReportingMapping extends AbstractMapping {
	
	/**
	 * @param array $result
	 * @return array
	 */
	public function mapStatistiqueByInstance($result) {
		$arrData = array();
		foreach($result as $value)  {
			$code = serialize($value['instance_id'].$value['domaine_id'].$value['type_action_id'].$value['semaine']);
			if(!isset($arrData[$code])) {
				$arrData[$code] = array (
						'instance_id' 		=> $value['instance_id'],
						'domaine_id'  		=> $value['domaine_id'],
						'type_action_id'   	=> $value['type_action_id'],
						'semaine'   		=> $value['semaine'],
						'utilisateur_id'	=> $value['porteur_id'],
						'structure_id'		=> $value['structure_id'],
						'data' 	   			=> array()
				);
			}
			if(!isset($arrData[$code]['data'][$value['type_statut']])) {
				$arrData[$code]['data'][$value['type_statut']] = 0;
			}
			$arrData[$code]['data'][$value['type_statut']] += (int)$value['nombre'];
		}
		return $arrData;
	}
	
	/**
	 * @param array $data
	 * @param string|number $type
	 * @return array
	 */
	public function mappingDataStatsEvo(&$data, $type) {
		$formule= $this->em->getRepository('OrangeMainBundle:Formule')->getTauxStats();
		$arrData = array($type => array(), 'taux' => array());
		for($i=1;$i<=Date('W');$i++) {
			$aide=false;
			foreach ($data as $value) {
				if ($i==$value['id'] ) {
					if(!isset($arrData[$type][$i])) {
						$arrData[$type][$value['id']] = array('libelle' => $value['id'], 'data' => array());
						$this->transfertDonnes($arrData[$type][$i], $value, false);
						if(isset($value['taux']))
							foreach ($value['taux'] as $key => $taux){
								$arrData[$type][$i]['data'][$key] = $taux;
								$arrData['taux'][$key] = $taux;
						}
						$aide=true;
					}
				}
			}
			if($aide==false) {
				if (!isset($arrData[$type][$i]) ) {
					$arrData[$type][$i] = array('libelle' => $i, 'data' => array());
					$this->transfertDonnes($arrData[$type][$i], $i, true);
					if(count($formule)>0)
						foreach ($formule as $for){
							$arrData[$type][$i]['data'][$for['libelle']] = 0;
							$arrData['taux'][$for['libelle']] = 0;
					}
				}
			}
		}
		return $arrData;
	}
	
	public function mappingDataStatsSimple(&$data, $type){
		$arrData = array($type => array(), 'taux' => array());
		foreach ($data as $value){
			$i=1;
			if (!isset($arrData[$type][$value['id']])){
				$arrData[$type][$value['id']] = array('libelle' => $value['libelle'], 'data' => array());
			}
			$this->transfertDonnes($arrData[$type][$value['id']], $value, false);
			if(isset($value['taux']))
				foreach ($value['taux'] as $key => $taux){
					$arrData[$type][$value['id']]['data'][$key] = $taux;
					$arrData['taux'][$key] = $taux;
					$i++;
			}
		}
		return $arrData;
	}
	
	/**
	 * le parametre type est soit instance ou structure
	 * le parametre params est utilise lors au cas ou on veut afficher
	 *  les structures ou instances qui n'ont pas de stats et qu'on vaut afficher
	 * @param array $data
	 * @param string|number $type
	 */
	public function mappingDataStats(&$data, $type, &$params, $bu=null) {
		$effectif= $this->em->getRepository('OrangeMainBundle:Utilisateur')->getUtilisateurByStructure($params, $bu)->getQuery()->getArrayResult();
		$effectifActif= $this->em->getRepository('OrangeMainBundle:Utilisateur')->getUtilisateurActifByStructure($params, $bu)->getQuery()->getArrayResult();
		$arrData = array($type => array(), 'taux' => array(), 'porteurs' => array());
		foreach($data as $value) {
			if(!isset($arrData[$type][$value['id']])) {
				$arrData[$type][$value['id']] = array(
						'libelle' => $value['libelle'], 'couleur' => (isset($value['couleur']) ? $value['couleur'] : 'FFFFFF'), 'data' => array()
					);
			}
			foreach(array_keys(Synthese::$formules) as $key) {
				$arrData[$type][$value['id']][$key] = $value[$key];
			}
			if(isset($value['porteurs']) && is_array($value['porteurs'])) {
				$arrData[$type][$value['id']]['porteurs'] = $value['porteurs'];
				foreach($value['porteurs'] as $porteurId => $porteur) {
					if(!isset($arrData['porteurs'][$porteurId])) {
						$arrData['porteurs'][$porteurId] = $porteur;
						continue;
					}
					foreach($arrData['porteurs'][$porteurId] as $key => $number) {
						$arrData['porteurs'][$porteurId][$key] = (is_numeric($porteur[$key])) ? ($porteur[$key] + $number) :  $porteur[$key];
					}
				}
			}
			$this->transfertDonnes($arrData[$type][$value['id']], $value, false);
			if(!empty($value['taux'])) {
				foreach ($value['taux'] as $key => $taux) {
					$arrData[$type][$value['id']]['data'][$key] = $taux;
					$arrData['taux'][$key] = $taux;
				}
				foreach(array_keys(Synthese::$formules) as $key) {
					$arrData[$type][$value['id']]['data'][$key] = $value[$key] ? round($value[$key], 1) : null;
					$arrData['taux'][$key] = $value[$key];
				}
			}
		}
		foreach ($params as $par) {
			if($type=="structure" && isset($arrData[$type][$par['id']])) {
				$this->addEffectifToStats($arrData[$type][$par['id']]['data'], $effectif, $effectifActif, $par);
			}
		}
		return $arrData;
	}
	
	public function addEffectifToStats(&$arrData, &$effectif, &$effectifActif, &$par){
		$test=false;
		foreach ($effectif as $eff) {
			if($eff['id']==$par['id']) {
				$test=true;
				$arrData['nbUsers']=$eff['usr'];
				$arrData['nbMoyenActionByUser']=intval($arrData['total']/$eff['usr']);
				break;
			}
		}
		if ($test==false) {
			$arrData['nbUsers']=0;
			$arrData['nbMoyenActionByUser']=0;
		}
		$test=false;
		foreach ($effectifActif as $eff) {
			if($eff['id']==$par['id']) {
				$arrData['nbUsersActif']=$eff['usr'];
				$arrData['nbMoyenActionByUserActif']=intval($arrData['total']/$eff['usr']);
				$test=true;break;
			}
		}
		if ($test==false) {
			$arrData['nbUsersActif'] = 0;
			$arrData['nbMoyenActionByUserActif'] = 0;
		}
	}
	
	/**
	 * le parametre type est soit instance ou structure
	 * le parametre typeCroise est soit instance ou semaine
	 * @param unknown $data
	 * @param unknown $type
	 */
	public function reportingInCroise(&$data, $type,$typeCroise,&$paramsType,&$paramsCroise){
		$arrData = array($type => array());
		foreach ($paramsType as $key=>$parT) {
			$arrData[$type][$parT['libelle']] = array($typeCroise => array(), 'taux' => array());
			foreach ($paramsCroise as $parC) {
				foreach ($data as $value) {
					if ($parT['id']==$value['f_id'] && $parC['id']==$value['s_id']) {
						if(!isset($arrData[$type][$parT['libelle']][$typeCroise][$value['s_id']])) {
							$arrData[$type][$parT['libelle']][$typeCroise][$value['s_id']] = array('libelle' => $value['s_libelle'], 'data' => array());
							$this->transfertDonnes($arrData[$type][$parT['libelle']][$typeCroise][$value['s_id']], $value, false);
							foreach($value['taux'] as $key => $taux) {
								$arrData[$type][$parT['libelle']][$typeCroise][$value['s_id']]['data'][$key] = $taux;
								$arrData[$type][$parT['libelle']]['taux'][$key] = $taux;
							}
							foreach(array_keys(Synthese::$formules) as $key) {
								$arrData[$type][$parT['libelle']][$typeCroise][$value['s_id']]['data'][$key] = $value[$key];
								$arrData[$type][$parT['libelle']]['taux'][$key] = $value[$key];
							}
							break;
						}
					}
				}
			}
		}
		
		return $arrData;
	}
	
	public function transfertDonnes(&$arrData, &$value=null,$isNull){
		if($isNull==false){
			$arrData['data']['nbAbandon'] = isset($value['nbAbandon']) ? $value['nbAbandon'] :0;
			$arrData['data']['nbDemandeAbandon'] = isset($value['nbDemandeAbandon']) ? $value['nbDemandeAbandon'] :0;
			$arrData['data']['nbFaiteDelai'] = isset($value['nbFaiteDelai']) ? $value['nbFaiteDelai'] :0;
			$arrData['data']['nbFaiteHorsDelai'] = isset($value['nbFaiteHorsDelai']) ? $value['nbFaiteHorsDelai'] :0;
			$arrData['data']['nbNonEchue'] = isset($value['nbNonEchue']) ? $value['nbNonEchue'] :0;
			$arrData['data']['nbEchueNonSoldee'] = isset($value['nbEchueNonSoldee']) ? $value['nbEchueNonSoldee'] :0 ;
			$arrData['data']['nbSoldeeHorsDelais'] = isset($value['nbSoldeeHorsDelais']) ? $value['nbSoldeeHorsDelais'] :0;
			$arrData['data']['nbSoldeeDansLesDelais'] = isset($value['nbSoldeeDansLesDelais']) ? $value['nbSoldeeDansLesDelais']: 0 ;
			$arrData['data']['nbActionNouvelle'] = isset($value['nbActionNouvelle']) ? $value['nbActionNouvelle'] :0;
			$arrData['data']['total'] = $value['total'];
		} else {
			$arrData['data'] = array(
					'nbActionNouvelle' => 0, 'nbNonEchue' => 0, 'nbEchueNonSoldee' => 0, 'nbAbandon' => 0, 'nbDemandeAbandon' => 0,
					'nbAbandon' => 0, 'nbFaiteDelai' => 0, 'nbFaiteHorsDelai' => 0, 'nbSoldeeDansLesDelais' => 0, 'nbSoldeeHorsDelais' => 0, 'total' => 0
				);
		}
	}
	
	public function transformRequeteToSimple(&$requete, &$params) {
		$data=array();
		$i=0;
		if(count($params)>0) {
			foreach($params as $val) {
				$data[$i]=array(  'id'=>$val['id'], 'libelle'=>$val['libelle'], 'nbDemandeAbandon' => 0, 'nbAbandon'=>0, 'nbActionNouvelle' => 0,
						'nbFaiteDelai'=>0, 'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0, 'nbEchueNonSoldee' =>0,
						'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'total'=>0
					);
				if(count($requete)>0) {
					foreach ($requete as $value){
						if($val['libelle']==$value['libelle'] ) {
							$data[$i]=$this->copieDonnees($value, $data[$i]);
						}
					}
				}
				$i++;
			}
		}
		return $data;
	}
	
	public function transformRequeteToPorteur(&$requete, &$params) {
		$data=array();
		$i=0;
		if(count($params)>0) {
			foreach($params as $val) {
				$data[$i]=array(
						'id'=>$val['id'], 'libelle'=>$val['libelle'], 'nbDemandeAbandon' => 0, 'nbActionNouvelle' => 0, 'nbAbandon'=>0, 'nbFaiteDelai'=>0,
						'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0, 'nbEchueNonSoldee' =>0,	'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'total'=>0
				);
				if(count($requete['data'])>0) {
					foreach ($requete['data'] as $value) {
						if($val['libelle']==$value['libelle'] ) {
							$data[$i]=$this->copieDonneesAlsoForPorteur($value, $data[$i], $requete['porteurs']);
						}
					}
				}
				$i++;
			}
		}
		return $data;
	}
	
	/**
	 * @param unknown $data
	 * @param unknown $instances
	 * @return array
	 */
	public function reportingByInstanceAndPorteur($data, $instances) {
		$arrData = array();
		foreach($data as $value) {
			if(!isset($arrData[$value['id']])) {
				$arrData[$value['id']] = array('id' => $value['id'], 'libelle' => $value['libelle'], 'couleur' => $value['couleur'], 'porteurs' => array());
			}
			$arrData[$value['id']] = $this->mergeWithSumByStatut($arrData[$value['id']], $value, Synthese::$fields);
			if(!isset($arrData[$value['id']]['porteurs'][$value['user_id']])) {
				$arrData[$value['id']]['porteurs'][$value['user_id']] = $value;
			}
			$arrData[$value['id']] = $this->mergeWithAvgByFormule($arrData[$value['id']], 'porteurs', Synthese::$formules);
		}
		return $arrData;
	}
	
	public function transformRequeteToSimpleNull(&$requete){
		$data = array('nbDemandeAbandon' => 0, 'nbAbandon'=>0,
				'nbFaiteDelai'=>0, 'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0, 'nbEchueNonSoldee' =>0, 'nbActionNouvelle' => 0,
				'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'total'=>0
			);
		if(count($requete)>0) {
			foreach ($requete as $value){
				$data=$this->copieDonnees($value, $data);
			}
		}
		return $data;
	}
	
	public function transformRequeteToCroise(&$requete, &$params1, &$params2){
		$data=array();
		$i=0;
		foreach ($params1 as $val){
			foreach($params2 as $valeur){
				$data[$i]=array(  'f_id'=>$val['id'], 'f_libelle'=>$val['libelle'], 'nbDemandeAbandon' => 0, 'nbAbandon'=>0,
						'nbFaiteDelai'=>0, 'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0, 'nbEchueNonSoldee' =>0,
						'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'total'=>0, 's_id'=>$valeur['id'],
						's_libelle'=>$valeur['libelle']
					);
				if(count($requete)>0){
					foreach ($requete as $value){
						if($val['id']==$value['f_id'] && $valeur['id']==$value['s_id']){
							$data[$i]=$this->copieDonnees($value, $data[$i]);
						}
					}
				}$i++;
			}
			
		}
		return $data;
	}
	
	public function copieDonnees($value, $data) {
		if($value['etatCourant']==Statut::ACTION_ABANDONNEE) {
			$data['nbAbandon'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_DEMANDE_ABANDON) {
			$data['nbDemandeAbandon'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_FAIT_DELAI) {
			$data['nbFaiteDelai'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_FAIT_HORS_DELAI) {
			$data['nbFaiteHorsDelai'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_NON_ECHUE){
			$data['nbNonEchue'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_ECHUE_NON_SOLDEE) {
			$data['nbEchueNonSoldee'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_SOLDEE_DELAI) {
			$data['nbSoldeeDansLesDelais'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_SOLDEE_HORS_DELAI) {
			$data['nbSoldeeHorsDelais'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_NOUVELLE) {
			$data['nbActionNouvelle'] = $value['total'];
		}
		$data['total'] +=$value['total'];
		return $data;
	}
	
	public function copieDonneesAlsoForPorteur($value, $data, $porteurs = array()) {
		$data['couleur'] =$value['couleur'];
		if(!isset($data['porteurs'][$value['user_id']])) {
			$data['porteurs'][$value['user_id']] = array(
					'nbDemandeAbandon' => 0, 'nbAbandon'=>0, 'nbFaiteDelai'=>0, 'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0,  'nbActionNouvelle' => 0,
					'nbEchueNonSoldee' =>0, 'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'total'=>0
			);
			$data['porteurs'][$value['user_id']]['libelle'] = isset($porteurs[$value['user_id']]) ? $porteurs[$value['user_id']] : null;
		}
		if($value['etatCourant']==Statut::ACTION_ABANDONNEE) {
			$data['nbAbandon'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbAbandon'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_DEMANDE_ABANDON) {
			$data['nbDemandeAbandon'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbDemandeAbandon'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_FAIT_DELAI) {
			$data['nbFaiteDelai'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbFaiteDelai'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_FAIT_HORS_DELAI) {
			$data['nbFaiteHorsDelai'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbFaiteHorsDelai'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_NON_ECHUE){
			$data['nbNonEchue'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbNonEchue'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_ECHUE_NON_SOLDEE) {
			$data['nbEchueNonSoldee'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbEchueNonSoldee'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_SOLDEE_DELAI) {
			$data['nbSoldeeDansLesDelais'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbSoldeeDansLesDelais'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_SOLDEE_HORS_DELAI) {
			$data['nbSoldeeHorsDelais'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbSoldeeHorsDelais'] = $value['total'];
		}
		if($value['etatCourant']==Statut::ACTION_NOUVELLE) {
			$data['nbActionNouvelle'] += $value['total'];
			$data['porteurs'][$value['user_id']]['nbActionNouvelle'] = $value['total'];
		}
		$data['total'] +=$value['total'];
		$data['porteurs'][$value['user_id']]['total'] +=$value['total'];
		return $data;
	}
	
	
	
	public function combineTacheAndActionByPorteur($data) {
		$arrData=array('data' => array(), 'porteurs' => array());
		$i=0;
		if(count($data)>0)
			foreach($data as $value) {
				$arrData['porteurs'][intval($value['user_id'])] = $value['porteur'];
				if(count($arrData['data'])<=0) {
					$arrData['data'][$i] = array(
							'id' => $value['id'], 'libelle' => $value['libelle'], 'couleur' => $value['couleur'], 'total' => intval($value['total']),
							'user_id' => intval($value['user_id']), 'porteur' => $value['porteur']
					);
					$arrData['data'][$i]['etatCourant']= ($value['tache_etat']==null) ? $value['action_etat'] : $value['tache_etat'];
				} else {
					$aide=false;
					for($j=0; $j<count($arrData['data']);$j++) {
						if($value['tache_etat']==null) {
							if($arrData['data'][$j]['etatCourant']==$value['action_etat'] && $arrData['data'][$j]['id']==$value['id'] && $arrData['data'][$j]['porteur']==$value['porteur']) {
								$arrData['data'][$j]['total']+=intval($value['total']);
								$aide=true;
								break;
							}
						} else {
							if($arrData['data'][$j]['etatCourant']==$value['tache_etat'] && $arrData['data'][$j]['id']==$value['id'] && $arrData['data'][$j]['porteur']==$value['porteur']) {
								$arrData['data'][$j]['total']=intval($value['total']);
								$aide=true;
								break;
							}
						}
					}
					if($aide==false) {
						$i++;
						$arrData['data'][$i] = array(
								'id' => $value['id'], 'libelle' => $value['libelle'], 'total' => intval($value['total']),
								'couleur' => $value['couleur'], 'user_id' => intval($value['user_id']), 'porteur' => $value['porteur']
							);
						$arrData['data'][$i]['etatCourant']= ($value['tache_etat']==null) ? $value['action_etat'] : $value['tache_etat'];
					}
					
				}
			}
		return $arrData;
	}
	
	public function combineTacheAndAction($data) {
		$arrData=array();
		$i=0;
		if(count($data)>0) {
			foreach($data as $value) {
				if(count($arrData)<=0) {
					$arrData[$i]=array('id'=>$value['id'], 'libelle'=>$value['libelle'], 'total'=>intval($value['total']));
					if($value['tache_etat']==null) {
						$arrData[$i]['etatCourant']=$value['action_etat'];
					} else {
						$arrData[$i]['etatCourant']=$value['tache_etat'];
					}
				} else {
					$aide=false;
					for($j=0; $j<count($arrData);$j++) {
						if($value['tache_etat']==null) {
							if($arrData[$j]['etatCourant']==$value['action_etat'] && $arrData[$j]['id']==$value['id']) {
								$arrData[$j]['total']+=intval($value['total']);
								$aide=true;
								break;
							}
						} else {
							if($arrData[$j]['etatCourant']==$value['tache_etat'] && $arrData[$j]['id']==$value['id']) {
								$arrData[$j]['total']+=intval($value['total']);
								$aide=true;
								break;
							}
						}
					}
					if($aide==false) {
						$i++;
						$arrData[$i]=array('id'=>$value['id'],'libelle'=>$value['libelle'], 'total'=>intval($value['total']));
						if($value['tache_etat']==null) {
							$arrData[$i]['etatCourant']=$value['action_etat'];
						} else {
							$arrData[$i]['etatCourant']=$value['tache_etat'];
						}
					}
				}
			}
		}
		return $arrData;
	}
	
	public function mapToHaveLibelle($stats){
		$req=array('Action Nouvelle'=>0,'faite délai'=>0, 'faite hors délai'=>0, 'soldée delai'=>0,'soldée hors delai'=>0, 'Echue non soldée'=>0, 'Demande Abandon'=>0, 'Abandonnée'=>0, 'Non échue'=>0 );
		foreach($stats as $stat) {
			if($stat['etatCourant']== Statut::ACTION_SOLDEE_DELAI) {
				$req['soldée delai']=$stat['total'];
			}elseif($stat['etatCourant']== Statut::ACTION_SOLDEE_HORS_DELAI) {
				$req['soldée hors delai']=$stat['total'];
			}elseif($stat['etatCourant']== Statut::ACTION_FAIT_DELAI) {
				$req['faite délai']=$stat['total'];
			}elseif($stat['etatCourant']== Statut::ACTION_FAIT_HORS_DELAI) {
				$req['faite hors délai']=$stat['total'];
			}elseif ($stat['etatCourant']== Statut::ACTION_ECHUE_NON_SOLDEE) {
				$req['Echue non soldée']=$stat['total'];
			}elseif ($stat['etatCourant']== Statut::ACTION_DEMANDE_ABANDON) {
				$req['Demande Abandon']=$stat['total'];
			}elseif ($stat['etatCourant']== Statut::ACTION_ABANDONNEE) {
				$req['Abandonnée']=$stat['total'];
			}elseif ($stat['etatCourant']== Statut::ACTION_NON_ECHUE) {
				$req['Non échue']=$stat['total'];
			}elseif ($stat['etatCourant']== Statut::ACTION_NOUVELLE) {
				$req['Action Nouvelle']=$stat['total'];
			}
		}
		return $req;
	}
	public function mapDataReportingInstance($data){
		$arrData = array('instance' => array(), 'taux' => array());
		foreach ($data as $value){
			if (!isset($arrData['instance'][$value['id_instance']])){
				$arrData['instance'][$value['id_instance']] = array('libelle' => $value['libelle'], 'data' => array());
			}
			$arrData['instance'][$value['id_instance']]['data']['nbAbandon'] = $value['nbAbandon'];
			$arrData['instance'][$value['id_instance']]['data']['nbDemandeAbandon'] = $value['nbDemandeAbandon'];
			$arrData['instance'][$value['id_instance']]['data']['nbFaiteDelai'] = $value['nbFaiteDelai'];
			$arrData['instance'][$value['id_instance']]['data']['nbFaiteHorsDelai'] = $value['nbFaiteHorsDelai'];
			$arrData['instance'][$value['id_instance']]['data']['nbEchueNonSoldee'] = $value['nbEchueNonSoldee'];
			$arrData['instance'][$value['id_instance']]['data']['nbNonEchue'] = $value['nbNonEchue'];
			$arrData['instance'][$value['id_instance']]['data']['nbSoldeeHorsDelais'] = $value['nbSoldeeHorsDelais'];
			$arrData['instance'][$value['id_instance']]['data']['nbSoldeeDansLesDelais'] = $value['nbSoldeeDansLesDelais'];
			$arrData['instance'][$value['id_instance']]['data']['total'] = $value['total'];
			foreach ($value['taux'] as $key => $taux){
				$arrData['instance'][$value['id_instance']]['data'][$key] = $taux;
				$arrData['taux'][$key] = $taux;
			}
			$arrData['taux']["Taux de respect du délai"] = 10;
		}
		return $arrData;
	}
	public function mapDataReportingStructure($data){
		$arrData = array('structure' => array(), 'taux' => array());
		foreach ($data as $value){
			if (!isset($arrData['structure'][$value['id_structure']])){
				$arrData['structure'][$value['id_structure']] = array('libelle' => $value['libelle'], 'data' => array());
			}
			$arrData['structure'][$value['id_structure']]['data']['nbAbandon'] = $value['nbAbandon'];
			$arrData['structure'][$value['id_structure']]['data']['nbDemandeAbandon'] = $value['nbDemandeAbandon'];
			$arrData['structure'][$value['id_structure']]['data']['nbFaiteDelai'] = $value['nbFaiteDelai'];
			$arrData['structure'][$value['id_structure']]['data']['nbFaiteHorsDelai'] = $value['nbFaiteHorsDelai'];
			$arrData['structure'][$value['id_structure']]['data']['nbEchueNonSoldee'] = $value['nbEchueNonSoldee'];
			$arrData['structure'][$value['id_structure']]['data']['nbNonEchue'] = $value['nbNonEchue'];
			$arrData['structure'][$value['id_structure']]['data']['nbSoldeeHorsDelais'] = $value['nbSoldeeHorsDelais'];
			$arrData['structure'][$value['id_structure']]['data']['nbSoldeeDansLesDelais'] = $value['nbSoldeeDansLesDelais'];
			$arrData['structure'][$value['id_structure']]['data']['total'] = $value['total'];
			foreach ($value['taux'] as $key => $taux){
				$arrData['structure'][$value['id_structure']]['data'][$key] = $taux;
				$arrData['taux'][$key] = $taux;
			}
			$arrData['taux']["Taux de respect du délai"] = 10;
		}
		return $arrData;
	}
	
	/**
	 * @param array $arrData
	 * @param array $values
	 * @param array $keys
	 * @return array
	 */
	private function mergeWithSumByStatut($arrData, $values, $keys = array()) {
		foreach($keys as $key) {
			if(isset($arrData[$key])==false && isset($values[$key])==false) {
				$arrData[$key] = 0;
			} elseif(isset($arrData[$key]) && isset($values[$key])) {
				$arrData[$key] += $values[$key];
			} elseif(isset($arrData[$key])==false) {
				$arrData[$key] = (int)$values[$key];
			}
		}
		return $arrData;
	}
	
	/**
	 * @param array $arrData
	 * @param array $values
	 * @param array $keys
	 * @return array
	 */
	private function mergeWithAvgByFormule($arrData, $code, $keys = array()) {
		foreach(array_keys($keys) as $key) {
			$number = 0;$sum = null;
			foreach($arrData[$code] as $data) {
				$number += $data[$key] ? 1 : 0;
				$sum     = $data[$key] ? ($sum ? $sum + $data[$key] : $data[$key]) : $sum;
			}
			$arrData[$key] = $sum ? number_format($sum / $number, 1) : null;
		}
		return $arrData;
	}
	
	/*public function mapStatistiqueByInstance($query) {
		$arrData = array('une_instance' => array());
		foreach($query as $key=> $value) {
			if(!isset($arrData['une_instance'][$value['instance_id']])) {
				$arrData['une_instance'][$value['instance_id']] = array (
						'instance_id' => $value['instance_id'], 'data' => array('type_statut' => array(), 'nombre' => array()
						));
			}
			$arrData['une_instance'][$value ['instance_id']]['data']['type_statut'][$key] = $value['type_statut'];
			$arrData['une_instance'][$value ['instance_id']]['data']['nombre'][$key] = $value['nombre'];
		}
		return $arrData;
	}*/
}
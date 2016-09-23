<?php

namespace Orange\MainBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;
use Gedmo\DoctrineExtensions;
use Doctrine\ORM\Query\Expr;
use DateTime;
use Doctrine\DBAL\Types\VarDateTimeType;

class Mapping 
{
	
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}	
	
public function exportAction($data, $dataStatut){
		$arrayStatut = array();
		foreach ($dataStatut as $statut){
			$arrayStatut[$statut->getCode()] = $statut->getLibelle();
		}
		
		$array = array();
		$i = 0;
		foreach ($data as $value){
			$j=1;
			$z=1;
			$cont = "";
			$ava = "";
			foreach ($value->getContributeur() as $contributeur){
				$cont = $cont."\n".$j.'. '.$contributeur->getUtilisateur()->getCompletNom();
				$j++;
			}
			foreach ($value->getAvancement() as $avancement){
				$ava = $ava."\n".$z.'. '.$avancement->getLibelle();
				$z++;
			}
			$array[$i] = array('reference' => $value->getReference(),'	' => $value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
								'libelle' => $value->getLibelle(),'description' => $value->getDescription(), 'priorite' => $value->getPriorite() ?$value->getPriorite()->getNiveau() :'' ,
									'porteur' =>$value->getPorteur()->__toString(),'direction' => $value->getPorteur()->getDirection(),'pole' => $value->getPorteur()->getPole(),
									'departement' => $value->getPorteur()->getDepartement(), 'service' => $value->getPorteur()->getService(),
									'type'=>$value->getTypeAction()->__toString().'#'.$value->getTypeAction()->getCouleur(),
									'statut' =>$arrayStatut[$value->getEtatCourant()], 'domaine' => $value->getDomaine()->__toString(), 'contributeur' => $cont
									, 'date_debut' => $value->getDateDebut()->format('d-m-Y'),'date_initial' => $value->getDateInitial()->format('d-m-Y'),
									'date_cloture' =>$value->getDateCloture() ? $value->getDateCloture()->format('d-m-Y') :'toujours en cours' ,'avancement'=>$ava,
									
					
					);
			$i++;
		}
		return $array;
	}
	
	public function exportSignalisation($data, $dataStatut){
		$arrayStatutSign = array();
		foreach ($dataStatut as $statut){
			$arrayStatutSign[$statut->getCode()] = $statut->getLibelle();
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			$array[$i] = array('ee' =>'ddd', 'reference' => $value->getReference(), 'Instance' => $value->getInstance()->getParent()? $value->getInstance()->getParent()->__toString().'#'.$value->getInstance()->getParent()->getCouleur():$value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
								'Périmétre' => $value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
								'Domaine' => $value->getDomaine()?$value->getDomaine()->__toString():'',
								'Type' => $value->getTypeSignalisation()?$value->getTypeSignalisation()->__toString().'#'.$value->getTypeSignalisation()->getCouleur():'##ffffff',
								'libelle' => $value->getLibelle(),'description' => $value->getDescription(),
								'source' => $value->getSource()->getUtilisateur()->getCompletNom(),'date_signale' =>  $value->getDateSignale()->format('d-m-Y'),'direction' => $value->getSource()->getUtilisateur()->getDirection(),
								'pole' => $value->getSource()->getUtilisateur()->getPole(),'departement' => $value->getSource()->getUtilisateur()->getDepartement(),
								'service' => $value->getSource()->getUtilisateur()->getService(),
								'statut' => $arrayStatutSign[$value->getEtatCourant()]
								
			);
			$i++;
		}
		return $array;
	}
	
	public function mapStatistiqueByInstance($query) 
	{
		$arrData = array();
		foreach($query as $value )  {
			$code = serialize($value['instance_id'].$value['domaine_id'].$value['type_action_id'].$value['semaine']);
			if(!isset($arrData[$code])) {
				$arrData[$code] = array (
								 'instance_id' 		=> $value['instance_id'],
								 'domaine_id'  		=> $value['domaine_id'],
								 'type_action_id'   => $value['type_action_id'],
								 'semaine'   		=> $value['semaine'],
								'utilisateur_id'	=> $value['porteur_id'],
								'structure_id'		=> $value['structure_id'],
								 'data' 	   		=> array()
						);
			}
			if(!isset($arrData[$code]['data'][$value['type_statut']])) {
				$arrData[$code]['data'][$value['type_statut']] = 0;
			}
			$arrData[$code]['data'][$value['type_statut']] += (int)$value['nombre'];
		}
		return $arrData;
	}
	
	public function mapDataforAlertDepassement($actions){
		$today = new \DateTime();
		$data = array();
		foreach ($actions as $action){
			$di = $action->getDateInitial();
			$dateDiff = $di->diff($today);
			if ($di < $today){
				$a = '+';
			}else
				$a = '-';
			$data[$action->getId()] = array('nom' => $action->getPorteur()->__toString(), 'email' => $action->getPorteur()->getEmail(),
					'manager' => $action->getPorteur()->getSuperior() ? $action->getPorteur()->getSuperior()->getEmail(): '',
					'instance' => $action->getInstance()->getLibelle(),
					'reference' => $action->getReference(), 'jours' => $a.$dateDiff->days, 'delai' => $action->getDateInitial()->format('d-m-Y'),
					'libelle' => $action->getLibelle(), 'id' => $action->getId()
			);
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			if(!isset($array['user'][$value['email']])) {
				$array['user'][$value['email']] = array('nom' => $action->getPorteur()->__toString(),
						'manager' => $action->getPorteur()->getSuperior() ? $action->getPorteur()->getSuperior()->getEmail(): '',
						'action' => array());
			}
			$array['user'][$value['email']]['action'][$i] = array('id'  => $value['id'], 'reference' => $value['reference'], 'libelle' => $value['libelle'],
					'instance' => $value['instance'], 'delai' => $value['delai'], 'jours' => $value['jours']
			);
			$i++;
		}
		return $array;
	}
	
	public function mapDataforAlertAnimateur($actions){
		$data = array();
		foreach ($actions as $action){
			$data[$action->getId()] = array('nom' => $action->getPorteur()->__toString(),'instance' => $action->getInstance()->getLibelle(),
					'animateur' => $action->getInstance()->getAnimateur(),'reference' => $action->getReference(),'delai' => $action->getDateInitial()->format('d-m-Y'),
					'libelle' => $action->getLibelle(), 'instance_id' => $action->getInstance()->getId(), 'action_id' => $action->getId()
			);
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			if(!isset($array['instance'][$value['instance_id']])) {
				$array['instance'][$value['instance_id']] = array('animateurs' => $value['animateur'],'instance' => $value['instance']
						,'action' => array());
			}
			$array['instance'][$value['instance_id']]['action'][$i] = array('id' =>  $value['action_id'], 'porteur' => $value['nom'], 'reference' => $value['reference'], 'libelle' => $value['libelle'],
					'delai' => $value['delai']
			);
			$i++;
		}
		return $array;
	}
	public function mapDataforRelances($signs){
		$data = array();
		foreach ($signs as $sign){
			$data[$sign->getId()] = array('libelle' => $sign->getLibelle(),'sign_id' =>$sign->getId(), 'instance' => $sign->getInstance()->getLibelle(),
					'animateurs' => $sign->getInstance()->getAnimateur(), 'date' => $sign->getDateSignale()->format('d-m-Y'), 'instance_id' => $sign->getInstance()->getId(),
					'reference' => $sign->getReference(), 'source' => $sign->getSource(), 'constateur' => $sign->getConstatateur(),
					'dateConstat' => $sign->getDateConstat()->format('d-m-Y')
			);
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			if (!isset($array['instance'][$value['instance_id']])){
				$array['instance'][$value['instance_id']] = array('animateurs' => $value['animateurs'], 'instance' => $value['instance'],
						'sign' => array()
				);
			}
			$array['instance'][$value['instance_id']]['sign'][$i] = array('id' =>  $value['sign_id'], 'libelle' => $value['libelle'],
					'date' => $value['date'], 'reference' => $value['reference'] , 'source' => $value['source'],
					'constateur' => $value['constateur'], 'dateConstat' => $value['dateConstat']
			);
			$i++;
		}
		return $array;
	}
	
	public function mapDataReportingInstance($data){
		$arrData = array('instance' => array(), 'taux' => array());
		foreach ($data as $value){
			$i=1;
			
			if (!isset($arrData['instance'][$value['id_instance']])){
				$arrData['instance'][$value['id_instance']] = array('libelle' => $value['libelle'], 'data' => array());
			}
			$arrData['instance'][$value['id_instance']]['data']['nbAbandon'] = $value['nbAbandon'];
			$arrData['instance'][$value['id_instance']]['data']['nbDemandeAbandon'] = $value['nbDemandeAbandon'];
			$arrData['instance'][$value['id_instance']]['data']['nbFaiteDelai'] = $value['nbFaiteDelai'];
			$arrData['instance'][$value['id_instance']]['data']['nbFaiteHorsDelai'] = $value['nbFaiteHorsDelai'];
			$arrData['instance'][$value['id_instance']]['data']['nbNonEchue'] = $value['nbNonEchue'];
			$arrData['instance'][$value['id_instance']]['data']['nbSoldeeHorsDelais'] = $value['nbSoldeeHorsDelais'];
			$arrData['instance'][$value['id_instance']]['data']['nbSoldeeDansLesDelais'] = $value['nbSoldeeDansLesDelais'];
			$arrData['instance'][$value['id_instance']]['data']['total'] = $value['total'];
			foreach ($value['taux'] as $key => $taux){
				$arrData['instance'][$value['id_instance']]['data'][$key] = $taux;
				$arrData['taux'][$key] = $taux;
				$i++;
			}
		}
		return $arrData;
	}
	public function mapDataReportingStructure($data){
		$arrData = array('structure' => array(), 'taux' => array());
		foreach ($data as $value){
			$i=1;
			if (!isset($arrData['structure'][$value['id_structure']])){
				$arrData['structure'][$value['id_structure']] = array('libelle' => $value['libelle'], 'data' => array());
			}
			$arrData['structure'][$value['id_structure']]['data']['nbAbandon'] = $value['nbAbandon'];
			$arrData['structure'][$value['id_structure']]['data']['nbDemandeAbandon'] = $value['nbDemandeAbandon'];
			$arrData['structure'][$value['id_structure']]['data']['nbFaiteDelai'] = $value['nbFaiteDelai'];
			$arrData['structure'][$value['id_structure']]['data']['nbFaiteHorsDelai'] = $value['nbFaiteHorsDelai'];
			$arrData['structure'][$value['id_structure']]['data']['nbNonEchue'] = $value['nbNonEchue'];
			$arrData['structure'][$value['id_structure']]['data']['nbSoldeeHorsDelais'] = $value['nbSoldeeHorsDelais'];
			$arrData['structure'][$value['id_structure']]['data']['nbSoldeeDansLesDelais'] = $value['nbSoldeeDansLesDelais'];
			$arrData['structure'][$value['id_structure']]['data']['total'] = $value['total'];
			foreach ($value['taux'] as $key => $taux){
				$arrData['structure'][$value['id_structure']]['data'][$key] = $taux;
				$arrData['taux'][$key] = $taux;
				$i++;
			}
		}
		return $arrData;
	}
}


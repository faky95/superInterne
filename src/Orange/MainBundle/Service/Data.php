<?php
namespace Orange\MainBundle\Service;

use DateTime;
use Orange\MainBundle\Entity\ActionStatut;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\InstanceUtils;
use Orange\MainBundle\Query\BaseQuery;
use Orange\MainBundle\Entity\InstanceHasTypeAction;

class Data extends BaseQuery {
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	protected $container;
	
	public function __construct($em, $container) {
		$this->container = $container;
		$this->em = $em;
	}	
	
	public function exportUtilisateur($data){
		$users = array();
		$i = 0;
		foreach ($data as $user){
			$profils = "";
			foreach ($user->getProfil() as $profil){
				$profils = $profils."\n".$profil;
			}
			$users[$i] = array('prenom' => $user->getPrenom(),'nom' => $user->getNom(),'structure' => $user->getStructure()->getName(), 'profil' => $profils,'etat' => $user->isEnabled());
			$i++;
		}
		return $users;
	}
	
	public function exportInstance($data){
		$array = array();
		$i = 0;
		foreach ($data as $value){
			$animateurs= $this->em->getRepository('OrangeMainBundle:Animateur')->findAnimateurs($value->getId());
			$domaines= $this->em->getRepository('OrangeMainBundle:InstanceHasDomaine')->findDomaines($value->getId());
			$types= $this->em->getRepository('OrangeMainBundle:InstanceHasTypeAction')->findtypes($value->getId());
			$j=1;
			$z=1;
			$k=1;
			$anim = "";
			$dom = "";
			$ty = "";
			foreach ($animateurs as $animateur){
				$anim = $anim."\n".$j.'. '.$animateur->getUtilisateur()->getCompletNom();
				$j++;
			}
			foreach ($domaines as $domaine){
				$dom = $dom."\n".$z.'. '.$domaine->getDomaine()->getLibelleDomaine();
				$z++;
			}
			foreach ($types as $type){
				$ty = $ty."\n".$k.'. '.$type->getTypeAction()->getType();
				$k++;
			}
			$array[$i] = array('libelle' => $value->getLibelle(),'type_instance' => $value->getTypeInstance()?$value->getTypeInstance()->getLibelle():null,
					'description' => $value->getDescription(),'animateur' => $anim, 'domaine' => $dom, 'type' => $ty, 'parent' => $value->getParent()?$value->getParent()->getLibelle():null,
						
						
			);
			$i++;
		}
		return $array;
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
				$ava = $ava."\n".$z.') '.$avancement->getDescription();
				$z++;
			}
			$array[$i] = array('reference' => $value->getReference(),'Insatnce' => $value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
								'libelle' => $value->getLibelle(),'description' => $value->getDescription(), 'priorite' => $value->getPriorite() ?$value->getPriorite()->getLibelle() :'' ,
									'porteur' =>$value->getPorteur()->__toString(),'direction' => $value->getPorteur()->getDirection(),'pole' => $value->getPorteur()->getPole(),
									'departement' => $value->getPorteur()->getDepartement(), 'service' => $value->getPorteur()->getService(),
									'type'=>$value->getTypeAction()->__toString().'#'.$value->getTypeAction()->getCouleur(),
									'statut' =>$arrayStatut[$value->getEtatReel()], 'domaine' => $value->getDomaine()->__toString(), 'contributeur' => $cont
									, 'date_debut' => $value->getDateDebut()? $value->getDateDebut()->format('d-m-Y'):'','date_initial' => $value->getDateInitial() ? $value->getDateInitial()->format('d-m-Y'):'',
									'date_cloture' =>$value->getDateCloture() ? $value->getDateCloture()->format('d-m-Y') :'En cours' ,'avancement'=>$ava,
									
					
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
			$actions= $this->em->getRepository('OrangeMainBundle:ActionHasSignalisation')->findActions($value->getId());
			$action = "";
			$j=1;
			if(!empty($actions)){
				foreach ($actions as $act){
					$action .= $j.') '.$act->getAction()->getReference()."\n";
					$j++;
				}
			}
			$array[$i] = array( 'reference' => $value->getReference(),'Instance' => $value->getInstance()->getParent()? $value->getInstance()->getParent()->__toString().'#'.$value->getInstance()->getParent()->getCouleur():$value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
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
	
	public function exportCanevas($data){
		$date = new \DateTime();
		$interval = new \DateInterval('P2W');
		$array = array();
		$i=0;
		foreach ($data as $value){
			$destinataire = InstanceUtils::animateursComplet($this->em, $value->getInstance());
			$array[$i] = array('reference' => $value->getReference(), 'porteur' => $destinataire['nom'][0], 'email' =>$destinataire['email'][0],'instance'=> $value->getInstance()->getParent()->__toString(),
					'contributeur' => '','statut' => 'action nouvelle',
					'type' => $value->getTypeSignalisation()?$value->getTypeSignalisation()->getType():null,
					'domaine' => $value->getDomaine()?$value->getDomaine()->getLibelleDomaine():null,
					'dateDeb' => $date->format('d/m/Y'),'dateInit' => $date->add($interval)->format('d/m/Y'),
					'dateCloture' => '','libelle' => $value->getLibelle(),'description' => $value->getDescription(),'priorite'=>'importante'
	
			);
			$i++;
		}
		return $array;
	}
	
	public function mapStatistiqueByInstance($query) 
	{
		$arrData = array('une_instance' => array());
		
		foreach ( $query as $key=> $value ) 
		{
			if(!isset($arrData['une_instance'][$value['instance_id']])) 
			{
				$arrData['une_instance'][$value['instance_id']] = array (
																		 'instance_id' => $value['instance_id'],
																		 'data' 	   => array('type_statut' => array(), 'nombre' => array())
																		);
			}
			$arrData['une_instance'][$value ['instance_id']]['data']['type_statut'][$key] = $value['type_statut'];
			$arrData['une_instance'][$value ['instance_id']]['data']['nombre'][$key] = $value['nombre'];
		}
		return $arrData;
	}
	
	public function mapDataforAlertDepassement($actions){
		$today = new \DateTime();
		$data = array();
		$i=0;
		foreach ($actions as $action){
			$di = $action->getDateInitial();
			$dateDiff = $di->diff($today);
			if ($di < $today){
				$a = '+'.$dateDiff->days;
			}elseif($dateDiff->days == 0){
				$a = $dateDiff->days;
			}else 
				$a = '-'.$dateDiff->days;
			$data[$i] = array('user_id' => $action->getPorteur()->getId(), 'nom' => $action->getPorteur()->getCompletNom(), 'email' => $action->getPorteur()->getEmail(),
					'manager' => $action->getPorteur()->getSuperior()? $action->getPorteur()->getSuperior()->getEmail():$action->getPorteur()->getEmail(), 'instance' => $action->getInstance()->getLibelle(),
					'reference' => $action->getReference(), 'jours' => $a, 'delai' => $action->getDateInitial()->format('d-m-Y'),
					'libelle' => $action->getLibelle(), 'id' => $action->getId()
			); 
			$i++;
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			if(!isset($array['user'][$value['user_id']])) {
				$array['user'][$value['user_id']] = array('action' => array());
			}
			$array['user'][$value['user_id']]['email_porteur'] = $value['email'];
			$array['user'][$value['user_id']]['porteur'] = $value['nom'];
			$array['user'][$value['user_id']]['manager'] = $value['manager'];
			$array['user'][$value['user_id']]['action'][$i] = array('id'  => $value['id'], 'reference' => $value['reference'], 'libelle' => $value['libelle'],
					'instance' => $value['instance'], 'delai' => $value['delai'], 'jours' => $value['jours']
			);
			$i++;
		}
		return $array;
	}
	
	public function mapDataforAlertAnimateur($actions){
		$data = array();
		foreach ($actions as $action){
			$data[$action->getId()] = array('nom' => $action->getPorteur()->getCompletNom(),'instance' => $action->getInstance()->getLibelle(),
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
	public function nouvelleSignalisation($signs){
		$data = array();
		foreach ($signs as $sign){
			$data[$sign->getId()] = array('libelle' => $sign->getLibelle(),'sign_id' =>$sign->getId(), 'instance' => $sign->getInstance()->getLibelle(),
					'animateurs' => $sign->getInstance()->getAnimateur(), 'date' => $sign->getDateSignale() ? $sign->getDateSignale()->format('d-m-Y'):'', 'instance_id' => $sign->getInstance()->getId(),
					'reference' => $sign->getReference(), 'source' => $sign->getSource(), 'constateur' => $sign->getConstatateur(),
					'dateConstat' =>  $sign->getDateConstat() ? $sign->getDateConstat()->format('d-m-Y') :''
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
			$arrData['instance'][$value['id_instance']]['data']['nbEchueNonSoldee'] = $value['nbEchueNonSoldee'];
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
			$arrData['structure'][$value['id_structure']]['data']['nbEchueNonSoldee'] = $value['nbEchueNonSoldee'];
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
	public function relanceNewAction($actions){
		$date2 = new \DateTime();
		$array = array();
		$i=0;
		foreach ($actions as $action){
			$animateurs = array();
			foreach ($action->getInstance()->getAnimateur() as $animateur){
				array_push($animateurs, $animateur->getUtilisateur()->getEmail());
			}
			$diff = $date2->diff($action->getDateAction());
			if($diff->format('%h') == 12){
                        $array[$i] = array('body' => array( 'id' => $action->getId(), 'ref' => $action->getReference()
							,'libelle' => $action->getLibelle(), 'instance' => $action->getInstance()->getLibelle()
							,'user' => $action->getPorteur()->getCompletNom()
							,'date' => $action->getDateInitial()->format('d-m-Y'))
							,'emailPorteur' => $action->getPorteur()->getEmail()
							,'emailManager' => $action->getPorteur()->getSuperior() ? $action->getPorteur()->getSuperior()->getEmail():''
							,'emailAnimateur' => $animateurs
					);
					$i++;
			}
        }
                
		return $array;
	}
	public function actionQuartTime($actions){
		$tabActions = array();
		foreach ($actions as $action){
			$today = date('Y-m-d');
			$a= new \DateTime();
			$now = intval($a->getTimestamp()/(3600*24));
			$date = strtotime($action['dateDebut']->format('Y-m-d'));
			$dateDeb= intval($action['dateDebut']->getTimestamp()/(3600*24));
			$dateIni= intval($action['dateInitial']->getTimestamp()/(3600*24));
			$interval = ($dateIni - $dateDeb);
			$echeance = $dateIni - $now;
			$val = $interval/4;
			for($i = 1; $i<=4;$i++){
				$a = intval($i*$val) ;
				$day =  date('Y-m-d', strtotime("+".$a." day", $date));
				if ($day == $today){
					array_push($action, $i);
					array_push($action, $echeance);
					array_push($tabActions, $action);
				}
			}
		}
		$array = array();
		$i=0;
		foreach ($tabActions as $action){
			if(!isset($array['user'][$action['email']])){
				$array['user'][$action['email']] = array('porteur' => $action['prenom'].' '.$action['nom'], 'action' => array());
			}
			$array['user'][$action['email']]['action'][$i] = $action;
			$i++;
		}
		return $array['user'];
		
	}
	public function validationAction($actions){
		$date2 = new \DateTime();
		$statut= $this->em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>Statut::ACTION_NON_ECHUE));
		$date2 = new \DateTime();
		$nbHeureActuel= intval($date2->getTimestamp()/3600);
		foreach ($actions as $action){
			$nbHeureDate=intval($action->getDateAction()->getTimestamp()/3600);
			if($nbHeureActuel-$nbHeureDate >= 24){
				$action->setEtatCourant(Statut::ACTION_NON_ECHUE);
				$action->setEtatReel(Statut::ACTION_NON_ECHUE);
			/*	$actionStatut=new ActionStatut();
				$actionStatut->setAction($action);
				$actionStatut->setStatut($statut);
				$actionStatut->setUtilisateur($action->getPorteur());*/
				//$this->em->persist($actionStatut);
				$this->em->persist($action);
				$this->em->flush();
			}
		}
	}
	
	public function priseEnChargeSignalisationAction($signs){
		$date2 = new \DateTime();
		$nbHeureActuel= intval($date2->getTimestamp()/3600);
		foreach ($signs as $sign){
			$nbHeureDate=intval($sign->getDateConstat()->getTimestamp()/3600);
			if($nbHeureActuel-$nbHeureDate >= 48){
				$sign->setEtatCourant(Statut::SIGNALISATION_PRISE_CHARGE);
				$this->em->persist($sign);
				$this->em->flush();
			}
		}
	}
}

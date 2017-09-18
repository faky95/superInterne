<?php
namespace Orange\MainBundle\Mapping;

use Orange\MainBundle\Entity\Statut;

class RelanceMapping extends AbstractMapping {
	
	/**
	 * @param unknown $signs
	 * @return array
	 */
	public function mapDataforRelances($signalisations) {
		$data = array();
		foreach ($signalisations as $signalisation) {
			$data[$signalisation->getId()] = array(
					'sign_id' =>$signalisation->getId(), 
					'libelle' => $signalisation->getLibelle(),
					'instance' => $signalisation->getInstance()->getLibelle(), 
					'animateurs' => $signalisation->getInstance()->getAnimateur(), 
					'date' => $signalisation->getDateSignale()->format('d-m-Y'), 
					'instance_id' => $signalisation->getInstance()->getId(),
					'reference' => $signalisation->getReference(), 
					'source' => $signalisation->getSource(), 
					'constateur' => $signalisation->getConstatateur(),
					'dateConstat' => $signalisation->getDateConstat()->format('d-m-Y')
			);
		}
		$array = array();
		$i=0;
		foreach ($data as $value){
			if (!isset($array['instance'][$value['instance_id']])){
				$array['instance'][$value['instance_id']] = array(
						'animateurs' => $value['animateurs'], 'instance' => $value['instance'], 'sign' => array()
					);
			}
			$array['instance'][$value['instance_id']]['sign'][$i] = array(
					'id' =>  $value['sign_id'], 'libelle' => $value['libelle'],
					'date' => $value['date'], 'reference' => $value['reference'] , 
					'source' => $value['source'], 'constateur' => $value['constateur'], 
					'dateConstat' => $value['dateConstat']
				);
			$i++;
		}
		return $array;
	}
	
	/**
	 * @param array $actions
	 * @return array
	 */
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
		foreach ($tabActions as $action) {
			if(!isset($array['user'][$action['email']])) {
				$array['user'][$action['email']] = array('porteur' => $action['prenom'].' '.$action['nom'], 'action' => array());
			}
			$array['user'][$action['email']]['action'][$i] = $action;
			$i++;
		}
		return isset($array['user']) ? $array['user'] : array();
	}
	
	public function validationAction($actions){
		$date2 = new \DateTime();
		$statut= $this->em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>Statut::ACTION_NON_ECHUE));
		$nbHeureActuel= intval($date2->getTimestamp()/3600);
		$nombre = 0;
		foreach ($actions as $action){
			$nbHeureDate=intval($action->getDateAction()->getTimestamp()/3600);
			if($nbHeureActuel-$nbHeureDate >= 24){
				$action->setEtatCourant(Statut::ACTION_NON_ECHUE);
				$action->setEtatReel(Statut::ACTION_NON_ECHUE);
				$this->em->persist($action);
				$nombre++;
			}
			if($nombre > 10) {
				break;
			}
		}
		$this->em->flush();
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
	
	public function mapDataforAlertDepassement($actions){
		$repo= $this->em->getRepository('OrangeMainBundle:ActionGenerique');
		$today = new \DateTime();
		$data = array();
		$i=0;
		foreach ($actions as $action){
			$di = $action->getDateInitial();
			$dateDiff = $di->diff($today);
			$a = $di < $today ? '+' : ($dateDiff->days == 0 ? '' : '-');
			$a = $dateDiff->days;
			$data[$i] = array(
					'user_id' => $action->getPorteur()->getId(),
					'nom' => $action->getPorteur()->getCompletNom(),
					'email' => $action->getPorteur()->getEmail(),
					'manager' => $action->getPorteur()->getSuperior() ? $action->getPorteur()->getSuperior()->getEmail() : $action->getPorteur()->getEmail(),
					'manager_id' => $action->getPorteur()->getSuperior() ? $action->getPorteur()->getSuperior()->getId() : null,
					'instance' => $action->getInstance()->getLibelle(),
					'reference' => $action->getReference(), 'jours' => $a, 'delai' => $action->getDateInitial()->format('d-m-Y'),
					'libelle' => $action->getLibelle(), 'id' => $action->getId(),
					'animateur'=>$action->getAnimateur()->getEmail(),
					'action_generique'=>($action->getActionGeneriqueHasAction()->count()>0 ? $action->getActionGeneriqueHasAction()->first()->getActionGenerique(): null)
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
			$array['user'][$value['user_id']]['porteurId'] = $value['user_id'];
			$array['user'][$value['user_id']]['porteur'] = $value['nom'];
			$array['user'][$value['user_id']]['manager'] = $value['manager'];
			$array['user'][$value['user_id']]['managerId'] = $value['manager_id'];
			$array['user'][$value['user_id']]['animateur'] = $value['animateur'];
			$array['user'][$value['user_id']]['action'][$i] = array('id'  => $value['id'], 'reference' => $value['reference'], 'libelle' => $value['libelle'],
					'instance' => $value['instance'], 'delai' => $value['delai'], 'jours' => $value['jours'],
					'action_generique'=>$value['action_generique']
			);
			if($value['action_generique']!=null){
				if(!isset($array['user'][$value['user_id']]['action_generique'][$value['action_generique']->getId()])) {
					$stats = $repo->getStatsSimpleActionByActionGenerique($value['action_generique']->getId())->getQuery()->getArrayResult();
					$map = $this->getReporting()->mapToHaveLibelle($stats);
					$array['user'][$value['user_id']]['action_generique'][$value['action_generique']->getId()] =
					array('ref'=>$value['action_generique']->getReference(), 'stats'=>$map,'nbActions'=>$value['action_generique']->getActionGeneriqueHasAction()->count());
				}
			}
			$i++;
		}
		return $array;
	}
	
	public function mapDataforAlertAnimateurGlobal($actions){
		$data = array();
		/** @var Action $action */
		foreach ($actions as $action){
			foreach ($action->getInstance()->getAnimateur() as $animateur){
				if(!isset($data[$animateur->getUtilisateur()->getId()]))
					$data[$animateur->getUtilisateur()->getId()]= array('email'=>$animateur->getUtilisateur()->getEmail(),
							'manager'=>$animateur->getUtilisateur()->getSuperior() ? $animateur->getUtilisateur()->getSuperior()->getEmail(): null,
							'action' => array('demande_abandon'=>  array('libelle'=>"Actions en demande d'abandon", 'data'=>array()),
									'demande_report' =>  array('libelle'=>"Actions en demande de report", 'data'=>array()),
									'faite'          =>  array('libelle'=>"Actions en attente de Cloture", 'data'=>array())));
					switch ($action->getEtatReel()){
						case Statut::ACTION_DEMANDE_ABANDON:
							if(!array_key_exists ($action->getId(), $data[$animateur->getUtilisateur()->getId()]['action']['demande_abandon']['data']))
								$data[$animateur->getUtilisateur()->getId()]['action']['demande_abandon']['data'][$action->getId()]=$action ;
								break;
								
						case Statut::ACTION_DEMANDE_REPORT:
							if(!array_key_exists($action->getId(), $data[$animateur->getUtilisateur()->getId()]['action']['demande_report']['data']))
								$data[$animateur->getUtilisateur()->getId()]['action']['demande_report']['data'][$action->getId()]=$action ;
								break;
								
						case Statut::ACTION_FAIT_HORS_DELAI:
						case Statut::ACTION_FAIT_DELAI:
							if(!array_key_exists($action->getId(), $data[$animateur->getUtilisateur()->getId()]['action']['faite']['data']))
								$data[$animateur->getUtilisateur()->getId()]['action']['faite']['data'][$action->getId()]=$action ;
								break;
					}
			}
		}
		
		return $data;
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
}
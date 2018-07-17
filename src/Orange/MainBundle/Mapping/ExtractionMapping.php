<?php
namespace Orange\MainBundle\Mapping;

use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\InstanceUtils;

class ExtractionMapping extends AbstractMapping {
	
	
	public function exportUtilisateur($data) {
		$users = array();
		$i = 0;
		foreach ($data as $user){
			$profils = "";
			foreach ($user->getProfil() as $profil){
				$profils = $profils."\n".$profil;
			}
			$users[$i] = array(
					'prenom' => $user->getPrenom(), 'nom' => $user->getNom(), 'profil' => $profils,
					'structure' => $user->getStructure()->getName(), 'etat' => $user->isEnabled()
				);
			$i++;
		}
		return $users;
	}
	
	public function exportInstance($data) {
		$array = array();
		$i = 0;
		foreach($data as $value) {
			$j = $z = $k = 1;
			$anim = $dom = $ty = null;
			foreach ($value->getAnimateur() as $animateur){
				$anim = $anim."\n".$j.'. '.$animateur->getUtilisateur()->getCompletNom();
				$j++;
			}
			foreach ($value->getDomaine() as $domaine){
				$dom = $dom."\n".$z.'. '.$domaine->getLibelleDomaine();
				$z++;
			}
			foreach ($value->getTypeAction() as $type){
				$ty = $ty."\n".$k.'. '.$type->getType();
				$k++;
			}
			$array[$i] = array(
					'libelle' => $value->getLibelle(), 'description' => $value->getDescription(), 'animateur' => $anim, 
					'type_instance' => $value->getTypeInstance() ? $value->getTypeInstance()->getLibelle() : null,
					'domaine' => $dom, 'type' => $ty, 'parent' => $value->getParent() ? $value->getParent()->getLibelle() : null
				);
			$i++;
		}
		return $array;
	}
	
	public function exportAction($data, $dataStatut) {
		$arrayStatut = array();
		foreach ($dataStatut as $statut){
			$arrayStatut[$statut->getCode()] = $statut->getLibelle();
		}
		
		$array = array();
		$i = 0;
		foreach ($data as $value) {
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
			$array[$i] = array(
					'reference' => $value->getReference(), 'libelle' => $value->getLibelle(),
					'Insatnce' => $value->getInstance()->__toString().'#'.$value->getInstance()->getCouleur(),
					'description' => $value->getDescription(), 'statut' =>$arrayStatut[$value->getEtatReel()], 
					'priorite' => $value->getPriorite() ? $value->getPriorite()->getLibelle() : '' ,
					'porteur' =>$value->getPorteur()->__toString(), 'contributeur' => $cont, 
					'direction' => $value->getPorteur()->getDirection(), 'avancement'=>$ava,
					'pole' => $value->getPorteur()->getPole(), 'domaine' => $value->getDomaine()->__toString(), 
					'departement' => $value->getPorteur()->getDepartement(), 'service' => $value->getPorteur()->getService(),
					'type'=>$value->getTypeAction()->__toString().'#'.$value->getTypeAction()->getCouleur(),
					'date_debut' => $value->getDateDebut() ? $value->getDateDebut()->format('d-m-Y') : '',
					'date_initial' => $value->getDateInitial() ? $value->getDateInitial()->format('d-m-Y') : '',
					'date_fin_prevue' => $value->getDateFinPrevue() ? $value->getDateFinPrevue()->format('d-m-Y') : '',
					'date_cloture' =>$value->getDateCloture() ? $value->getDateCloture()->format('d-m-Y') : 'En cours',
				);
			$i++;
		}
		return $array;
	}
	
	public function exportSignalisation($data, $dataStatut) {
		$arrayStatutSign = array();
		foreach ($dataStatut as $statut) {
			$arrayStatutSign[$statut->getCode()] = $statut->getLibelle();
		}
		$array = array();
		$i=0;
		/**
		 * @var Signalisation $value
		 */
		foreach ($data as $value) {
			$action = "";
			$j=1;
			foreach($value->getAction() as $act) {
				$action .= $j.') '.$act->getReference()."\n";
				$j++;
			}
			$instance = $value->getInstance();
			var_dump($value->getSource()->getUtilisateur()->getStructure());exit;
			$array[$i] = array(
					'reference' => $value->getReference(),
					'Instance' => $instance->getParent() 
						? $instance->getParent()->__toString().'##'.$value->getInstance()->getParent()->getCouleur() 
						: $instance->__toString().'##'.$instance->getCouleur(),
					'Périmétre' => $instance->__toString().'##'.$instance->getCouleur(),
					'Domaine' => $value->getDomaine() ? $value->getDomaine()->__toString() : '',
					'Type' => $value->getTypeSignalisation() ? $value->getTypeSignalisation()->__toString().'##'.$value->getTypeSignalisation()->getCouleur() : '###ffffff',
					'libelle' => $value->getLibelle(),'description' => $value->getDescription(),
					'source' => $value->getSource()->getUtilisateur()->getCompletNom(),
					'date_signale' =>  $value->getDateSignale()->format('d-m-Y'),
					'direction' => $value->getSource()->getUtilisateur()->getDirection(),
					'pole' => $value->getSource()->getUtilisateur()->getPole(),
					'departement' => $value->getSource()->getUtilisateur()->getDepartement(),
					'service' => $value->getSource()->getUtilisateur()->getService(),
					'statut' => $arrayStatutSign[$value->getEtatCourant()], 'action' => $action ,
					'motif' => ($value->getEtatCourant()==Statut::SIGNALISATION_INVALIDER ? $value->getSignStatut()->last()->getCommentaire() : 'aucun')
				);
			$i++;
		}
		return $array;
	}
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function exportCanevas($data) {
		$interval = new \DateInterval('P2W');
		$array = array();
		$i=0;
		foreach ($data as $value){
			$date = new \DateTime();
			$destinataire = InstanceUtils::animateursComplet($this->em, $value->getInstance());
			$array[$i] = array(
					'reference' => $value->getReference(), 'porteur' => $destinataire['nom'][0], 'email' =>$destinataire['email'][0],
					'instance'=> $value->getInstance()->getParent() ? $value->getInstance()->getParent()->__toString() : null,
					'contributeur' => '', 'statut' => 'action nouvelle',
					'type' => $value->getTypeSignalisation() ? $value->getTypeSignalisation()->getType() : null,
					'domaine' => $value->getDomaine() ? $value->getDomaine()->getLibelleDomaine() : null,
					'dateDeb' => $date->format('d/m/Y'), 'dateInit' => $date->add($interval)->format('d/m/Y'), 'dateCloture' => '',
					'libelle' => $value->getLibelle(),'description' => $value->getDescription(),'priorite'=>'importante'
				);
			$i++;
		}
		return $array;
	}
}
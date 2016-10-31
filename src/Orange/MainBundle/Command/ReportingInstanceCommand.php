<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;

class ReportingInstanceCommand extends BaseCommand {

	protected  $statuts= array(
			'nbAbandon' => 'Abandonnée',
			'nbDemandeAbandon' => "Demande d'abandon",
			'nbFaiteDelai' => "Faite dans les délais",
			'nbFaiteHorsDelai' => "Faite hors délai",
			'nbNonEchue' => "Non échue",
			'nbEchueNonSoldee' => "Echue non soldée",
			'nbSoldeeHorsDelais' => 'Soldée hors délais',
			'nbSoldeeDansLesDelais' => "Soldée dans les délais",
			'total' => "Total",
	);
	
	protected function configure(){
		parent::configure();
		$this->setName($this->getName() . ':reporting_instance')->setDescription('envoi des reporting automatisé');
	}

	public function getStatus($bu){
		$formule=$this->getEntityManager()->getRepository('OrangeMainBundle:Formule')->getTauxStats($bu);
		$statuts=$this->statuts;
		if(count($formule)>0)
			foreach ($formule as $key=>$form)
				$statuts[$form['libelle']]=$form['libelle'];
				return $statuts;
	
	}
	
	public function mapIds($data){
		$array = array();
		foreach($data as $value){
			array_push($array, $value['id']);
		}
		return $array;
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getEnvoiInstance();
		$utilisateurs = $em->getRepository('OrangeMainBundle:Utilisateur')->getAllDestinataireOfReporting();
		$mapUsers = array();
		foreach ($utilisateurs as $usr){
			$mapUsers[$usr->getId()] = $usr->getEmail();
		}
		$pas = $em->getRepository('OrangeMainBundle:Pas')->listAllPas()->getQuery()->execute();
		$etats = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$per = array();
		$statuts = array(
				'nbAbandon' => 'Abandon',
				'nbDemandeAbandon' => "Demande d'abandon",
				'nbFaiteDelai' => "Faite dans les délais",
				'nbFaiteHorsDelai' => "Faite hors délai",
				'nbNonEchue' => "Non échue",
				'nbSoldeeHorsDelais' => 'Soldée hors délai',
				'nbSoldeeDansLesDelais' => "Soldée dans les délais",
				'total' => "Total",
			);
		foreach ($pas as $value) {
			$per[$value->getId()] = $value->getLibelle();
		}
		foreach ($envois as $envoi) {
			    $objWriter=null;
				$dest = array();
				$query = $this->getEntityManager()->createQuery($envoi->getReporting()->getRequete());
				$params= unserialize($envoi->getReporting()->getParameter());
				$query->setParameters($params);
				$actions = null;
				if ($envoi->getReporting()->getQuery()){
					$query2 = $this->getEntityManager()->createQuery($envoi->getReporting()->getQuery());
					$query2->setParameters($params);
					$idActions = $this->mapIds($query2->execute());
					$actions = $em->getRepository('OrangeMainBundle:Action')->filterExportReporting($idActions);
				}
				
				$req = $this->get('orange.main.dataStats')->combineTacheAndAction($query->getArrayResult());
				$arrType=unserialize($envoi->getReporting()->getArrayType());
				$map= $this->get('orange.main.dataStats')->transformRequeteToSimple($req,$arrType );
				$bu = $envoi->getReporting()->getUtilisateur()->getStructure()->getBuPrincipal();
				$data = $this->get('orange.main.calcul')->stats($bu, $map);
				$data = $this->get('orange.main.dataStats')->mappingDataStats($data, 'instance',$arrType, $bu);
				$objWriter = $this->get('orange.main.reporting')->reportinginstanceAction($data, $this->getStatus($bu), $actions, $etats->getQuery()->execute());
				$filename = $envoi->getReporting()->getLibelle().date("Y-m-d_H-i").'.xlsx';
			$i=0;
			foreach ($envoi->getReporting()->getDestinataire() as $destinataire) {
				$dest[$i] = $mapUsers[$destinataire->getId()];
				$i++;
			}
			$objWriter->save($this->getContainer()->get('kernel')->getRootDir()."//..//web//upload//reporting//$filename");
			$sub = "Reporting ".$per[$envoi->getReporting()->getPas()->getId()];
			$result = $this->getMailer()->sendReport($dest, $sub, $filename);
			$chemin = LogsMailUtils::LogOnFileMail($result, $sub, $dest);
		}
		if (!empty($chemin)){
			$send = $this->getMailer()->sendLogsMail(
					"Journal sur les reporting par instance",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig",
							array('libelle'=>" reportings par instance")),$chemin);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}

}
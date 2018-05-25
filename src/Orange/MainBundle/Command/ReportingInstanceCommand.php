<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;
use Symfony\Component\Console\Input\InputOption;

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
	
	protected function configure() {
		parent::configure();
		$this->setName($this->getName() . ':reporting_instance')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des reporting automatisé');
	}

	public function getStatus($bu){
		$formule=$this->getEntityManager()->getRepository('OrangeMainBundle:Formule')->getTauxStats($bu);
		$statuts=$this->statuts;
		if(count($formule)>0) {
			foreach ($formule as $form) {
				$statuts[$form['libelle']]=$form['libelle'];
			}
		}
		return $statuts;
	}
	
	public function mapIds($data){
		$array = array();
		foreach($data as $value){
			array_push($array, $value['id']);
		}
		return $array;
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$reportingMapping = $this->getMapping()->getReporting()->setEntityManager($em);
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getEnvoiInstance($bu, $espace, $projet);
		$utilisateurs = $em->getRepository('OrangeMainBundle:Utilisateur')->getAllDestinataireOfReporting($bu, $espace, $projet);
		$mapUsers = array();
		foreach ($utilisateurs as $usr){
			$mapUsers[$usr->getId()] = $usr->getEmail();
		}
		$pas = $em->getRepository('OrangeMainBundle:Pas')->listAllPas()->getQuery()->execute();
		$etats = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$per = array();
		foreach ($pas as $value) {
			$per[$value->getId()] = $value->getLibelle();
		}
		foreach ($envois as $envoi) {
			$sub = "Reporting ".$per[$envoi->getReporting()->getPas()->getId()];
			try {
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
				$req = $reportingMapping->combineTacheAndAction($query->getArrayResult());
				$arrType=unserialize($envoi->getReporting()->getArrayType());
				$map= $reportingMapping->transformRequeteToSimple($req, $arrType);
				$bu = $envoi->getReporting()->getUtilisateur()->getStructure()->getBuPrincipal();
				$data = $this->get('orange.main.calcul')->stats($bu, $map);
				$data = $reportingMapping->mappingDataStats($data, 'instance', $arrType, $bu);
				$objWriter = $this->get('orange.main.reporting')->reportinginstanceAction($data, $this->getStatus($bu), $actions, $etats->getQuery()->execute());
				$filename = $envoi->getReporting()->getLibelle().date("Y-m-d_H-i").'.xlsx';
				$i=0;
				foreach ($envoi->getReporting()->getDestinataire() as $destinataire) {
					$dest[$i] = $mapUsers[$destinataire->getId()];
					$i++;
				}
				$objWriter->save($this->getContainer()->get('kernel')->getRootDir()."//..//web//upload//reporting//$filename");
				$result = $this->getMailer()->sendReport($dest, $sub, $filename);
				$chemin = LogsMailUtils::LogOnFileMail($result, $sub, $dest);
			} catch(\Exception $e) {
				$this->getMailer()->send(array("madiagne.sylla@orange-sonatel.com","mamekhady.diouf@orange-sonatel.com"), array(), 
						"Erreur sur le reporting par instance :: ".$envoi->getReporting()->getLibelle(), $e->getMessage()
					);
				continue;
			}
		}
		if(!empty($chemin)) {
			$this->getMailer()->sendLogsMail(
					"Journal sur les reporting par instance",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig", array('libelle'=>" reportings par instance")), $chemin
				);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}

}

<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;
use Symfony\Component\Config\Definition\Exception\Exception;

class ReportingStructureCommand extends BaseCommand {
	protected $statuts = array(
			'nbAbandon' => 'Abandonnée',
			'nbDemandeAbandon' => "Demande d'abandon",
			'nbFaiteDelai' => "Faite dans les délais",
			'nbFaiteHorsDelai' => "Faite hors délai",
			'nbNonEchue' => "Non échue",
			'nbEchueNonSoldee' => "Echue non soldée",
			'nbSoldeeHorsDelais' => 'Soldée hors délais',
			'nbSoldeeDansLesDelais' => "Soldée dans les délais",
			'total' => "Total" 
	);
	protected function configure() {
		parent::configure();
		$this->setName($this->getName() . ':reporting_structure')
			->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
			->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
			->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
			->setDescription('envoi des reporting automatisé');
	}
	public function getStatus($bu) {
		$formule = $this->getEntityManager()->getRepository('OrangeMainBundle:Formule')->getTauxStats($bu);
		$statuts = $this->statuts;
		if(count($formule) > 0) {
			foreach($formule as $form) {
				$statuts [$form ['libelle']] = $form ['libelle'];
			}
		}
		return $statuts;
	}
	public function mapIds($data) {
		$array = array();
		foreach($data as $value) {
			array_push($array, $value ['id']);
		}
		return $array;
	}
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getEnvoiStructure($bu, $espace, $projet);
		$utilisateurs = $em->getRepository('OrangeMainBundle:Utilisateur')->getAllDestinataireOfReporting();
		$mapUsers = array();
		foreach($utilisateurs as $usr) {
			$mapUsers [$usr->getId()] = $usr->getEmail();
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
				'total' => "Total" 
		);
		foreach($pas as $value) {
			$per [$value->getId()] = $value->getLibelle();
		}
		foreach($envois as $envoi) {
			$sub = "Reporting " . $per [$envoi->getReporting()->getPas()->getId()];
			try {
				$dest = array();
				$query = $this->getEntityManager()->createQuery($envoi->getReporting()->getRequete());
				$query->setParameters(unserialize($envoi->getReporting()->getParameter()));
				$actions = null;
				if($envoi->getReporting()->getQuery()) {
					$query2 = $this->getEntityManager()->createQuery($envoi->getReporting()->getQuery());
					$query2->setParameters(unserialize($envoi->getReporting()->getParameter()));
					$idActions = $this->mapIds($query2->execute());
					$actions = $em->getRepository('OrangeMainBundle:Action')->filterExportReporting($idActions);
				}
				$req = $this->get('orange.main.dataStats')->combineTacheAndAction($query->getArrayResult());
				$arrType = unserialize($envoi->getReporting()->getArrayType());
				$map = $this->get('orange.main.dataStats')->transformRequeteToSimple($req, $arrType);
				$bu = $envoi->getReporting()->getUtilisateur()->getStructure()->getBuPrincipal();
				$data = $this->get('orange.main.calcul')->stats($bu, $map);
				$data = $this->get('orange.main.dataStats')->mappingDataStats($data, 'structure', $arrType, $bu);
				$objWriter = $this->get('orange.main.reporting')->reportingstructureAction($data, $this->getStatus($bu), $actions, $etats->getQuery()->execute());
				$filename = $envoi->getReporting()->getLibelle() . '-' . date("Y-m-d_H-i") . '.xlsx';
				$i = 0;
				foreach($envoi->getReporting()->getDestinataire() as $destinataire) {
					$dest[$i] = $mapUsers[$destinataire->getId()];
					$i ++;
				} 
				$objWriter->save("./web/upload/reporting/$filename");
				$result = $this->getMailer()->sendReport($dest, $sub, $filename);
				$chemin = LogsMailUtils::LogOnFileMail($result, $sub, $dest);
			} catch(\Exception $e) {
				$this->getMailer()->send(array("madiagne.sylla@orange-sonatel.com","mamekhady.diouf@orange-sonatel.com"), array(), 
						"Erreur sur le reporting par instance :: ".$envoi->getReporting()->getLibelle(), $e->getMessage()
					);
				continue;
			}
		}
		if(! empty($chemin)) {
			$send = $this->getMailer()->sendLogsMail("Journal sur les reporting par structures", $this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig", array(
					'libelle' => " reportings par structure" 
			)), $chemin);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
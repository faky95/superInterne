<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Command\BaseCommand;

class StatistiqueCommand extends BaseCommand
{
	
	protected function configure()
	{
		parent::configure();
		$this->setName('chargement:statistique')
			 ->setDescription('Chargement de la table statistique')
			 ->addArgument(
			 		'all', InputArgument::OPTIONAL,
			 		'Faut il prendre toutes les statistiques depuis le début de l\'année ?'
			 );
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$today = new \DateTime();
		$today = $today->format('Y-m-d');
		$tempRow = array('annee' => date("Y", strtotime("-7 day", strtotime($today))));
		$connexion = $this->get('database_connection');
		ini_set('memory_limit','256M');
		$em = $this->getEntityManager();
		$stats = $em->getRepository('OrangeMainBundle:Action')->getAllActions();
		$em->getRepository('OrangeMainBundle:Statistique')->createQueryBuilder('s')->delete()
			->where('s.semaine = :semaine')
			->andWhere('s.annee = :annee')
			->setParameters(array('semaine' => date('W'), 'annee' => date('Y')))
			->getQuery()
			->execute();
		if(!empty($stats)) {
			$statistiquePorteur = $this->getMapping()->getReporting()->mapStatistiqueByInstance($stats);
			$tempRow['type_id'] 				= 1;
			$statistiquerow = $this->emplileStatutRow($statistiquePorteur, $tempRow);
			foreach($statistiquerow as $row) {
				$connexion->insert('statistique', $row);
			}
		}
	
		$output->writeln(utf8_encode('Commande exécutée avec succès !'));
	}
	
	
	
	public function emplileStatutRow($statistiqueStatut, $tempRow) {
		$arrData = array();
		foreach ($statistiqueStatut as $stat) {
			$total = 0;
			$statistiquerow = array(
					'instance_id' => 0,	'domaine_id' => 0, 'type_action_id' => 0, 'structure_id' => 0,
					'utilisateur_id' => 0, 'type_id' => 0, 'nb_faite' => 0, 'nb_soldee' => 0, 'total' => 0,
					'semaine' => 0, 'annee' => 0, 'nb_en_cours' => 0, 'abandonnee' => 0, 'demande_abandon' => 0, 'nb_demande_report' => 0,
					'echue_non_soldee' => 0, 'non_echue_non_soldee' => 0, 'nb_fait_delai' => 0, 'nb_fait_hors_delai' => 0,
					'non_echue' => 0, 'nb_solde_delai' => 0, 'nb_solde_hors_delai' => 0
				);
			$statistiquerow = array_merge($statistiquerow, $tempRow);
			$statistiquerow['instance_id'] 			= $stat['instance_id'];
			$statistiquerow['domaine_id'] 			= $stat['domaine_id'];
			$statistiquerow['structure_id'] 			= $stat['structure_id'];
			$statistiquerow['utilisateur_id'] 			= $stat['utilisateur_id'];
			$statistiquerow['type_action_id'] 		= $stat['type_action_id'];
			$statistiquerow['semaine'] 				= $stat['semaine'];
			if(isset($stat['data'][Statut::ACTION_ABANDONNEE])) {
				$nombre_abandon 					= $stat['data'][Statut::ACTION_ABANDONNEE];
				$statistiquerow['abandonnee'] 		= $nombre_abandon;
				$total += $nombre_abandon;
			}
			if(isset($stat['data'][Statut::ACTION_DEMANDE_ABANDON])) {
				$nombre_demande_abandon 					= $stat['data'][Statut::ACTION_DEMANDE_ABANDON];
				$statistiquerow['demande_abandon'] 			= $nombre_demande_abandon;
				$total += $nombre_demande_abandon;
			}
			if(isset($stat['data'][Statut::ACTION_DEMANDE_REPORT])) {
				$nombre_demande_report 					= $stat['data'][Statut::ACTION_DEMANDE_REPORT];
				$statistiquerow['nb_demande_report'] 			= $nombre_demande_report;
				$total += $nombre_demande_report;
			}
			if(isset($stat['data'][Statut::ACTION_ECHUE_NON_SOLDEE])) {
				$nombre_echue_non_soldee 					= $stat['data'][Statut::ACTION_ECHUE_NON_SOLDEE];
				$statistiquerow['echue_non_soldee'] 		= $nombre_echue_non_soldee;
				$total += $nombre_echue_non_soldee;
			}
			if(isset($stat['data'][Statut::ACTION_FAIT_DELAI])) {
				$nb_fait_delai 						= $stat['data'][Statut::ACTION_FAIT_DELAI];
				$statistiquerow['nb_fait_delai'] 	= $nb_fait_delai;
				$total += $nb_fait_delai;
			}
			if(isset($stat['data'][Statut::ACTION_FAIT_HORS_DELAI])) {
				$nb_fait_hors_delai 					= $stat['data'][Statut::ACTION_FAIT_HORS_DELAI];
				$statistiquerow['nb_fait_hors_delai'] 	= $nb_fait_hors_delai;
				$total += $nb_fait_hors_delai;
			}
			if(isset($stat['data'][Statut::ACTION_SOLDEE_DELAI])) {
				$nb_solde_delai 						= $stat['data'][Statut::ACTION_SOLDEE_DELAI];
				$statistiquerow['nb_solde_delai'] 	= $nb_solde_delai;
				$total += $nb_solde_delai;
			}
			if(isset($stat['data'][Statut::ACTION_SOLDEE_HORS_DELAI])) {
				$nb_solde_hors_delai 					= $stat['data'][Statut::ACTION_SOLDEE_HORS_DELAI];
				$statistiquerow['nb_solde_hors_delai'] 	= $nb_solde_hors_delai;
				$total += $nb_solde_hors_delai;
			}
			if(isset($stat['data'][Statut::ACTION_NON_ECHUE])) {
				$nombre_non_echue 					= $stat['data'][Statut::ACTION_NON_ECHUE];
				$statistiquerow['non_echue'] 		= $nombre_non_echue;
				$statistiquerow['nb_en_cours'] 		= $nombre_non_echue;
				$total += $nombre_non_echue;
			}
			$statistiquerow['total'] = $total;
			$arrData[] = $statistiquerow;
		}
		return $arrData;
	}
	
}



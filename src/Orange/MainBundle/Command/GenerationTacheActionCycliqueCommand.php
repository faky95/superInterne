<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Orange\MainBundle\Entity\ActionCyclique;
use Orange\MainBundle\Entity\Tache;
use Symfony\Component\Console\Output\OutputInterface;

class GenerationTacheActionCycliqueCommand extends BaseCommand {
	
	protected function configure() {
		parent::configure();
		$this->setName('super:generation:tache')
			->setDescription('génération des tâches des actions cycliques')
			->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
			->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
			->addOption('bu', 'b', InputOption::VALUE_OPTIONAL);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\MainBundle\Command\BaseCommand::execute()
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$timestamp= (new \DateTime("NOW"))->getTimestamp();
		$pas = $this->getContainer()->getParameter('pas');
		$actionCycliques = array();
		// get actions cycliques
		if(date('D', $timestamp) === 'Mon' && date('j', $timestamp) === '1') {
			$actionCycliques = $em->getRepository('OrangeMainBundle:ActionCyclique')->tacheToGenerate($bu, $projet, $espace);
		} elseif(date('D', $timestamp) === 'Mon') { //check if monday
			$periodicites = array($pas['Hebdomadaire'] , $pas['Quinzaine']);
			$actionCycliques = $em->getRepository('OrangeMainBundle:ActionCyclique')->tacheByPeriodicite($bu, $projet, $espace, $periodicites);
		} elseif(date('j', $timestamp) === '1') { //check if premier du mois
			$periodicites = array($pas['Mensuelle'] , $pas['Trimestrielle'], $pas['Semestrielle']);
			$actionCycliques = $em->getRepository('OrangeMainBundle:ActionCyclique')->tacheByPeriodicite($bu, $projet, $espace, $periodicites);
		} else {
			$actionCycliques = $em->getRepository('OrangeMainBundle:ActionCyclique')->findWithoutTache($bu, $projet, $espace);
		}
		foreach($actionCycliques as $actionCyclique) {
			if($actionCyclique->getPas()==null) {
				continue;
			}
			$tache = $actionCyclique->newTache($this->getContainer()->getParameter('pas'));
			if($tache==null) {
				continue;
			}
			$em->persist($tache);
	    	$contributeurs = array();
	    	foreach($tache->getActionCyclique()->getAction()->getContributeur() as $contributeur) {
	    		$contributeurs[] = $contributeur->getUtilisateur()->getEmail();
	    	}
			$this->get('orange.main.mailer')->notifNewTache(array($actionCyclique->getAction()->getPorteur()->getEmail()), $contributeurs, $tache);
		}
		$em->flush();
		//check if they have at least on occurence
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

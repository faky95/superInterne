<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Orange\MainBundle\Entity\ActionCyclique;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Tache;

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
		// get actions cycliques
		$actionCycliques = $em->getRepository('OrangeMainBundle:ActionCyclique')->tacheToGenerate($bu, $projet, $espace);
		foreach($actionCycliques as $actionCyclique) {
			if($actionCyclique->getPas()==null) {
				continue;
			}
			$tache = $actionCyclique->newTache($this->getContainer()->getParameter('pas'));
			$em->persist($tache);
			$this->get('orange.main.mailer')->notifNewTache(array('madiagne.sylla@orange-sonatel.com'), $tache);
		}
		$em->flush();
		//check if they have at least on occurence
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

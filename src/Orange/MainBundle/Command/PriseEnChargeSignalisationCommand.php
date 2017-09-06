<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Signalisation;

class PriseEnChargeSignalisationCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName().':prise_en_charge_signalisation')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('prise en charge des signalisations');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$signs = $em->getRepository('OrangeMainBundle:Signalisation')->nouvelleSignalisation($bu, $projet, $espace);
		$this->getMapping()->getRelance()->setEntityManager($em)->priseEnChargeSignalisationAction($signs);
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}

}
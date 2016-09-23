<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidationActionCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName().':validation_action')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('relance le porteur pour la prise en charge');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->nouvelleAction($bu, $projet, $espace);
		$this->get('orange.main.data')->validationAction($actions);
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}

}
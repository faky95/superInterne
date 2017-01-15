<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerationTacheActionCycliqueCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName().':generation:tache')
				->setDescription('generation des tâches des actions cycliques');
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Orange\MainBundle\Command\BaseCommand::execute()
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		
		// get actions cycliques
		
		//check if they have at least on occurence
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
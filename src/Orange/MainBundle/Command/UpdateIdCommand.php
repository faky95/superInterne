<?php

namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Query\ActionQuery;

class UpdateIdCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':update_id');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$query = new ActionQuery($em->getConnection());
		$table = array('instance_has_domaine','instance_has_type_action','action_has_signalisation');
		foreach ($table as $t){
			$query->updateId($t);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
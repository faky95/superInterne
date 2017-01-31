<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Query\ActionQuery;

class MiseJourEntityCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName().':update_relation');
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$query = new ActionQuery($em->getConnection());
		$query->miseAJourEntity();
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
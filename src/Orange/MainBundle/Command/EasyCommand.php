<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EasyCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':easy')
				->setDescription('envoi des alertes aux animateurs');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
			$to = "fatoukine.ndao@orange-sonatel.com";
			$subject = "Test";
			$body = "ceci est un test";
			$this->getMailer()->sendEasy($to,$subject, $body);
			
			$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':test')
				->setDescription('envoi des relances');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$to = 'Abdouaziz.ndaw@orange-sonatel.com';
		$subject = 'Relance';
		$this->getMailer()->send($to, $subject);
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
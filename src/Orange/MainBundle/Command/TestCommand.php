<?php

namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Tdb;

class TestCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':test')
				->setDescription('envoi des relances');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$to = 'Abdouaziz.ndaw@orange-sonatel.com';
		$subject = 'Relance';
		$content  = "Ceci est un est.";
// 		$body = $this->getTemplating()->render( array(
// 				'content' => $content
// 			));
			$result = $this->getMailer()->send($to, $subject);
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
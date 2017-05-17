<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand extends BaseCommand
{
	protected $baseCommandName = 'super:khady';

	protected function configure()
	{
		parent::configure();
		$this
		->setName($this->baseCommandName)
		->setDescription('premier cmd by maaxady');
	}
	public function execute(InputInterface $input, OutputInterface $output){
		$output->writeln(utf8_encode('Hello  World !'));
	}

}
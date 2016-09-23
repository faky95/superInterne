<?php

namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class BaseCommand extends ContainerAwareCommand
{
	protected $baseCommandName = 'super:relance';

	protected function configure()
	{
		$this
		->setName($this->baseCommandName)
		->setDescription('Gestion des relances');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln($this->getDescription());
	}

	public function get($service) {
		return $this->getContainer()->get($service);
	}
// 	public function getRepository()
	public function getEntityManager() {
		return $this->get('doctrine.orm.entity_manager');
	}

	public function getTemplating() {
		return $this->get('templating');
	}

	public function getMailer() {
		return $this->get('orange.main.mailer');
	}

}
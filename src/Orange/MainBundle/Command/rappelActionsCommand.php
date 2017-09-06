<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class rappelActionsCommand extends BaseCommand {
	
	protected function configure(){
		parent::configure();
		$this->setName($this->getName() . ':rappel_porteur')
			->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
			->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
			->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
			->setDescription('envoi des rappels aux porteurs');
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$states = $this->getContainer()->getParameter('states');
		$actions = $em->getRepository('OrangeMainBundle:Action')->userToAlertRappel($bu, $projet, $espace, $states);
		$data = $this->getMapping()->getRelance()->setEntityManager($em)->mapDataforAlertDepassement($actions);
		foreach($data['user'] as $user){
			$body = $this->getTemplating()->render('OrangeMainBundle:Action:rappelActions.html.twig', array(
						'porteur' => $user['nom'], 'actions' => $user['action'],
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$this->getMailer()->sendRappel('madiagne.sylla@orange-sonatel.com', "Rappel d'actions", $body);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

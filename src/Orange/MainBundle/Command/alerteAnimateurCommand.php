<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class alerteAnimateurCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':alerte_animateur')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des alertes aux animateurs');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->userToAlertAnimateur($bu, $projet, $espace);
		$data = $this->getMapping()->getRelance()->mapDataforAlertAnimateur($actions);
		foreach($data['instance'] as $instance) {
			$animateurs = array();
			foreach ($instance['animateurs'] as $animateur){
				array_push($animateurs, $animateur->getUtilisateur()->getEmail());
			}
			$cc = array();
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alertAnimateur.html.twig', array(
						'actions' => $instance['action'],'instance' => $instance['instance'],
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$this->getMailer()->sendRappel($animateurs, $cc, 'Attente de cloture des actions pour '.$instance['instance'], $body);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
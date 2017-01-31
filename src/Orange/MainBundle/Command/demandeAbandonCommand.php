<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class demandeAbandonCommand extends BaseCommand {
	
	protected function configure(){
		parent::configure();
		$this->setName($this->getName().':demande_abandon')
			->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
			->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
			->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
			->setDescription('envoi des alertes aux animateurs');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->alertAnimateurForAbandon($bu, $projet, $espace);
		$data = $this->get('orange.main.data')->mapDataforAlertAnimateur($actions);
		foreach($data['instance'] as $instance) {
            $animateurs = array();
			foreach ($instance['animateurs'] as $animateur){
				array_push($animateurs, $animateur->getUtilisateur()->getEmail());
			}
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:demandeAbandon.html.twig', array(
						'actions' => $instance['action'],'instance' => $instance['instance'],
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$this->getMailer()->sendRappel($animateurs, "Demande d'abandon", $body);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

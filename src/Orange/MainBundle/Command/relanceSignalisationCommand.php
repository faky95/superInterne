<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class relanceSignalisationCommand extends BaseCommand {
	
	protected function configure(){
		parent::configure();
		$this->setName($this->getName() . ':signalisation')
			->setDescription('envoi relances signalisations');
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$states = $this->getContainer()->getParameter('states');
		$signs = $em->getRepository('OrangeMainBundle:Signalisation')->signalisationAValider($states);
		$data = $this->getMapping()->getRelance()->mapDataforRelances($signs);
		foreach($data['instance'] as $instance) {
            $animateurs = array();
			foreach ($instance['animateurs'] as $animateur){
				array_push($animateurs, $animateur->getUtilisateur()->getEmail());
			}
			$body = $this->getTemplating()->render('OrangeMainBundle:Signalisation:relanceSignalisation.html.twig', array(
						'signs' => $instance['sign'],'instance' => $instance['instance'],
						'accueil_url' => $this->getContainer()->get('router')->generate('accueil', array(), true)
					));
			$this->getMailer()->sendRappel($animateurs, 'Attente de prise en charge signalisations  pour l\'instance '.$instance['instance'], $body);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

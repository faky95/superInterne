<?php

namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class nouvelleSignalisationCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':nouvelle_signalisation')
				->setDescription('prise en charge de nouvelles signalisations');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$signs = $em->getRepository('OrangeMainBundle:Signalisation')->nouvelleSignalisation();
		$data = $this->get('orange.main.data')->nouvelleSignalisation($signs);
		foreach($data['instance'] as $key => $instance){
            $animateurs = array();
			foreach ($instance['animateurs'] as $animateur){
				array_push($animateurs, $animateur->getUtilisateur()->getEmail());
			}
			$to = $animateurs;
			$subject = "Prise en charge d'une signalisation.";
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:nouvelleSignalisation.html.twig', array(
						'signs' => $instance['sign'],'instance' => $instance['instance'],
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$result = $this->getMailer()->sendRappel($to, $subject, $body);
		}
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
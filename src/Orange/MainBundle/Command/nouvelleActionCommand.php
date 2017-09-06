<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class nouvelleActionCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName().':nouvelle_action')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('relance le porteur pour la prise en charge');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->nouvelleAction($bu, $projet, $espace);
		$data = $this->getMapping()->getRelance()->relanceNewAction($actions);
		foreach($data as $action) {
            $to = $action['emailPorteur'];
			$cc = array($action['emailManager'], $action['emailAnimateur']); 
			$subject = 'Prise en charge d\'une action';
			$body = array('action' => $action['body'],
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true));
            $this->getMailer()->sendRelanceNewAction($to, $cc, $subject, $body);
		}
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
<?php

namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class atteinteDelaiCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':atteinte_delai')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des alertes depassement');
		
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->atteintDelai($bu, $projet, $espace);
		$data = $this->get('orange.main.data')->mapDataforAlertDepassement($actions);
		foreach($data['user'] as $key => $user){
			$nbr =  count($user['action']);
			$to = $user['email_porteur'];
			$cc = array($user['manager']);
			$subject = 'Actions échues';
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:atteinteDelai.html.twig', array(
						'porteur' => $user['porteur'], 'actions' => $user['action'], 'nbr' => $nbr,
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$result = $this->getMailer()->send($to, $cc, $subject, $body);
		}
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
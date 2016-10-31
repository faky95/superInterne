<?php	
namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;

class alerteQuartTimeCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':quart_time')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des rappels quart temps');
		
	}
	public function execute(InputInterface $input, OutputInterface $output){
		$tour = 	array('premier', 'deuxiéme', 'troisiéme', 'dernier');
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->alertQuartTime($bu, $projet, $espace);
		$tabUsersActions= $this->get('orange.main.data')->actionQuartTime($actions);
		foreach ($tabUsersActions as $key => $user){
			$to = $key;
			$subject = "Rappel Quart Temps";
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alerteQuartTime.html.twig', array(
					'porteur' => $user['porteur'], 'actions' => $user['action'], 'tour' => $tour,
					'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
				));
			$result = $this->getMailer()->sendAlerteQuartTime($to,$subject, $body);
			$chemin = LogsMailUtils::LogOnFileMail($result, $subject, array($to));
		}
		if (!empty($chemin)){
			$send = $this->getMailer()->sendLogsMail(
					"Journal sur les relances des Rappel Quart Temps",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig",
							array('libelle'=>"relances les rappels Quart Temps")),$chemin);
		 }
		 
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
<?php	
namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\QuickMakingBundle\Annotation\QMLogger;
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
		echo 'zzz';exit;
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->alertQuartTime($bu, $projet, $espace);
		$tabUsersActions= $this->get('orange.main.data')->actionQuartTime($actions);
		echo count($tabUsersActions);exit;
		foreach ($tabUsersActions as $user){
			var_dump($user);exit;
			$to = $action['email'];
			$subject = "Rappel Quart Temps";
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alerteQuartTime.html.twig', array(
					'porteur' => $action['prenom'].' '.$action['nom'], 'action' => $action,
					'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
				));
		$result = $this->getMailer()->sendAlerteQuartTime($to,$subject, $body);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
// 	public function execute(InputInterface $input, OutputInterface $output){
// 		$espace = $input->getOption('espace');
// 		$bu = $input->getOption('bu');
// 		$projet = $input->getOption('projet');
// 		$em = $this->getEntityManager();
// 		$states = $this->getContainer()->getParameter('states');
// 		$actions = $em->getRepository('OrangeMainBundle:Action')->userToAlertDepassement($bu, $projet, $espace);
// 		$data = $this->get('orange.main.data')->mapDataforAlertDepassement($actions);
// 		foreach($data['user'] as $key => $user){
// 			$nbr =  count($user['action']);
// 			$to = $user['email_porteur'];
// // 			$cc = $user['manager'];
// 			$cc = array($user['manager'],
// 						'fode.mar@orange-sonatel.com'
// 			);
// 			$subject = 	"Notification de rappel d'échéances";
// 			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alertDepassement.html.twig', array(
// 						'porteur' => $user['porteur'], 'actions' => $user['action'],'nbr' => $nbr,
// 						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
// 					));
// 			$result = $this->getMailer()->sendAlert($to, $cc, $subject, $body);
// 		}
			
// 		$output->writeln(utf8_encode('Yes! ça marche'));
// 	}
}
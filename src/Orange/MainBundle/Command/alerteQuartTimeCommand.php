<?php	
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;
use Orange\MainBundle\Entity\Notification;
use Orange\MainBundle\Entity\TypeNotification;

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
		$actions = $this->getEntityManager()->getRepository('OrangeMainBundle:Action')->alertQuartTime($bu, $projet, $espace);
		$tabUsersActions= $this->getMapping()->getRelance()->actionQuartTime($actions);
		$index = 0;
		foreach ($tabUsersActions as $key => $user) {
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alerteQuartTime.html.twig', array(
					'porteur' => $user['porteur'], 'actions' => $user['action'], 'tour' => $tour,
					'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
				));
			$result = $this->getMailer()->sendAlerteQuartTime($key, "Rappel Quart Temps", $body);
			$chemin = LogsMailUtils::LogOnFileMail($result, "Rappel Quart Temps", array($key));
			$em->persist(Notification::nouvelleInstance(
					count($user['action']), $em->getReference('OrangeMainBundle:TypeNotification', TypeNotification::$ids['rappelQuartTemps']),
					array($em->getRepository('OrangeMainBundle:Utilisateur')->findOneBy(array('email' => $key))),
					array(), $result
				));
			if($index % 10 == 0) {
				$em->flush();
				sleep(6);
			}
			$index++;
		}
		if(!empty($chemin)) {
			$this->getMailer()->sendLogsMail(
					"Journal sur les relances des Rappel Quart Temps",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig",
							array('libelle'=>"relances les rappels Quart Temps")),$chemin);
		 }
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

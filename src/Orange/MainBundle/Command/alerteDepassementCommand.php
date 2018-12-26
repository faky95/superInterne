<?php	
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;
use Orange\MainBundle\Entity\Notification;
use Orange\MainBundle\Entity\TypeNotification;

class alerteDepassementCommand extends BaseCommand {
	
	protected function configure() {
			parent::configure();
			$this->setName($this->getName() . ':alerte_depassement')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des alertes depassement');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->userToAlertDepassement($bu, $projet, $espace);
		$data = $this->getMapping()->getRelance()->setEntityManager($em)->mapDataforAlertDepassement($actions);
		$index = 0;
		//var_dump($action); exit();
		$mailer = $this->get('mailer');
		$spool = $mailer->getTransport()->getSpool();
		$transport = $this->get('swiftmailer.transport.real');
		foreach($data['user'] as $user) {
			$subject = 	"Notification de rappel d'echeances";
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alertDepassement.html.twig', array(
						'porteur' => $user['porteur'], 'data' => $user, 'nbr' => count($user['action']),
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			try {
				$result = $this->getMailer()->send($user['email_porteur'], $user['manager'], $subject, $body);
				$chemin = LogsMailUtils::LogOnFileMail($result, $subject, array($user['email_porteur']), array($user['manager']), count($user['action']));
				$spool->flushQueue($transport);
			} catch(\Exception $e) {
				$result = false;
			}
			$em->persist(Notification::nouvelleInstance(
					count($user['action']), $em->getReference('OrangeMainBundle:TypeNotification', TypeNotification::$ids['relanceDepassement']),
					$user['porteurId'] ? array($em->getReference('OrangeMainBundle:Utilisateur', $user['porteurId'])) : array(),
					$user['managerId'] ? array($em->getReference('OrangeMainBundle:Utilisateur', $user['managerId'])) : array(), $result
				));
			if($index % 10 == 0) {
				$em->flush();
				sleep(6);
			}
			$index++;
		}
		if(!empty($chemin)) {
			$this->getMailer()->sendLogsMail("Journal sur les relances des rappels d'échéances",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig", array('libelle'=>"rappel d'échéances")), $chemin);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

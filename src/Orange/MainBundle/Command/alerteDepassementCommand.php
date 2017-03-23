<?php	
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;

class alerteDepassementCommand extends BaseCommand {
	
	protected function configure(){
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
		$data = $this->get('orange.main.data')->mapDataforAlertDepassement($actions);
		foreach($data['user'] as $user) {
			$subject = 	"Notification de rappel d'echeances";
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alertDepassement.html.twig', array(
						'porteur' => $user['porteur'], 'data' => $user,'nbr' => count($user['action']),
						'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
					));
			$result = $this->getMailer()->send($user['email_porteur'], array($user['manager']), $subject, $body, true);
			$chemin = LogsMailUtils::LogOnFileMail($result, $subject, array($user['email_porteur']), array($user['manager']), count($user['action']));
		}
		if(!empty($chemin)) {
			$this->getMailer()->sendLogsMail("Journal sur les relances des rappels d'échéances",
					$this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig",
							array('libelle'=>"rappel d'échéances")),$chemin);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

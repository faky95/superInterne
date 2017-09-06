<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Utils\LogsMailUtils;

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
		$data = $this->getMapping()->getRelance()->setEntityManager($em)->mapDataforAlertDepassement($actions);
		foreach($data['user'] as $user) {
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:atteinteDelai.html.twig', array(
					'porteur' => $user['porteur'], 'actions' => $user['action'], 'nbr' => count($user['action']),
					'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
				));
			$result = $this->getMailer()->send($user['email_porteur'], array($user['manager']), 'Actions échues', $body);
			$chemin = LogsMailUtils::LogOnFileMail($result, 'Actions échues', array($user['email_porteur']), array(array($user['manager'])), count($user['action']));
		}
		if(!empty($chemin)) {
			$this->getMailer()->sendLogsMail("Journal sur les relances des Actions échues",
					                $this->getTemplating()->render("OrangeMainBundle:Relance:logsMailSend.html.twig",
					                 array('libelle'=>"relances des Actions échues")), $chemin);
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}
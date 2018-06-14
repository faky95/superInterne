<?php	
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\TypeNotification;
use Orange\MainBundle\Entity\Notification;

class AlertesForAnimatorCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':alertes_animators')
				->addOption('projet', 'p', InputOption::VALUE_OPTIONAL)
				->addOption('espace', 'es', InputOption::VALUE_OPTIONAL)
				->addOption('bu', 'b', InputOption::VALUE_OPTIONAL)
				->setDescription('envoi des alertes sur actions en attente de validation (report, abandon, en attente de solde ) ');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$espace = $input->getOption('espace');
		$bu = $input->getOption('bu');
		$projet = $input->getOption('projet');
		$em = $this->getEntityManager();
		$actions = $em->getRepository('OrangeMainBundle:Action')->alertAnimateurGlobal($bu, $espace, $projet)->getQuery()->execute();
		$data = $this->getMapping()->getRelance()->mapDataforAlertAnimateurGlobal($actions);
		$index = 0;
		foreach($data as $id => $value) {
			$to = array($value['email']);
			$cc = $value['manager']!=null ? array($value['manager']) : null;
			$body = $this->getTemplating()->render('OrangeMainBundle:Relance:alertAnimateurGlobal.html.twig', array(
					'actions' => $value['action'],'accueil_url' => $this->getContainer()->get('router')->generate('dashboard', array(), true)
			));
			$result = $this->getMailer()->sendRappel($to, $cc, 'Traitement sur les actions de mes instances', $body);
			$em->persist(Notification::nouvelleInstance(
					count($value['action']), $em->getReference('OrangeMainBundle:TypeNotification', TypeNotification::$ids['relanceValidation']),
					array($em->getReference('OrangeMainBundle:Utilisateur', $id)),
					array(), $result
				));
			if($index % 10 == 0) {
				$em->flush();
				sleep(6);
			}
			$index++;
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
}

<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Envoi;

class EnvoiCommand extends BaseCommand {
	
	protected function configure() {
		parent::configure();
		$this->setName($this->getName().':create_envoi')
			->setDescription('génère les envois');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$today = new \DateTime();
		$em = $this->getEntityManager();
		$pas = $this->getContainer()->getParameter('pas');
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getEnvoi();
		foreach ($envois as $envoi) {
			if ($today->format('Y-m-d') == $envoi->getDateEnvoi()->format('Y-m-d')) {
				$p = $envoi->getReporting()->getPas()->getId();
				if($p == $pas['Mensuelle']) {
					$dateEnvoi = date('Y-m-d', strtotime("+1 month", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Bimestrielle']) {
					$dateEnvoi = date('Y-m-d', strtotime("+2 month", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Trimestrielle']) {
					$dateEnvoi = date('Y-m-d', strtotime("+3 month", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Quadrimestrielle']) {
					$dateEnvoi = date('Y-m-d', strtotime("+4 month", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Semestrielle']) {
					$dateEnvoi = date('Y-m-d', strtotime("+6 month", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Hebdomadaire']) {
					$dateEnvoi = date('Y-m-d', strtotime("+1 week", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Quinzaine']) {
					$dateEnvoi = date('Y-m-d', strtotime("+2 week", strtotime($today->format('Y-m-d'))));
				} elseif($p == $pas['Journaliere']) {
					$dateEnvoi = date('Y-m-d', strtotime("+1 day", strtotime($today->format('Y-m-d'))));
				}
				$object = new Envoi();
				$object->setReporting($envoi->getReporting());
				$object->setPeriodicite($envoi->getPeriodicite());
				$object->setTypeReporting($envoi->getTypeReporting());
				$object->setDateEnvoi(new \DateTime($dateEnvoi));
				$em->persist($object);
				$em->flush();
			}
		}
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
	
}

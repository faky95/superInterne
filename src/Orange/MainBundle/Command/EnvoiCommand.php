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
		$em = $this->getEntityManager();
		$pas = $this->getContainer()->getParameter('pas');
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getLastsEnvoi()->getQuery()->execute();
		foreach ($envois as $envoi) {
			    $dateEnvoi = $this->checkDateNextEnvoi($envoi['envoi']);
				$object = new Envoi();
				$object->setReporting($envoi['envoi']->getReporting());
				$object->setPeriodicite($envoi['envoi']->getPeriodicite());
				$object->setTypeReporting($envoi['envoi']->getTypeReporting());
				$object->setDateEnvoi($dateEnvoi);
				$em->persist($object);
				
		}
		$em->flush();
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
	
	/**
	 * 
	 * @param Envoi $envoi
	 */
	public function checkDateNextEnvoi($envoi){
		$today = new \DateTime("now");
		$p        = $envoi->getReporting()->getPas()->getId();
		$pas      = $this->getContainer()->getParameter('pas');
		if($p == $pas['Mensuelle']) {
			$marge = "+1 month";
		} elseif($p == $pas['Bimestrielle']) {
			$marge = "+2 month";
		} elseif($p == $pas['Trimestrielle']) {
			$marge = "+3 month";
		} elseif($p == $pas['Quadrimestrielle']) {
			$marge = "+4 month";
		} elseif($p == $pas['Semestrielle']) {
			$marge = "+6 month";
		} elseif($p == $pas['Hebdomadaire']) {
			$marge = "+1 week";
		} elseif($p == $pas['Quinzaine']) {
			$marge ="+2 week";
		} elseif($p == $pas['Journaliere']) {
			$marge = "+1 day";
		}else{
			$marge = "+0 day";
		}
	    $nextDate = $envoi->getDateEnvoi()->format('Y-m-d');
		while (strtotime($nextDate) <  strtotime($today->format('Y-m-d'))) {
			$nextDate = date('Y-m-d', strtotime($marge, strtotime($nextDate)));
		}
		return new \DateTime($nextDate);
	}
	
}

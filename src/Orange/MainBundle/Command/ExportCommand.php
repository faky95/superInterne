<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName('super:export:action')->setDescription("envoi des extractions d'action automatisé");
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$extractions = $em->getRepository('OrangeMainBundle:Extraction')->findByEtat(0);
		foreach($extractions as $extraction) {
			$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
			$query = $this->getEntityManager()->createQuery($extraction->getQuery());
			$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
			$query->setParameters(unserialize($extraction->getParam()));
			$actions       = $query->getArrayResult();
			$objWriter = $this->get('orange.main.extraction')->exportAction($actions, $statut->getQuery()->execute());
			$filename = 'actions_'.date("Y-m-d_H-i-s").'.xlsx';
			$objWriter->save("./web/upload/reporting/$filename");
			$sub = "Extraction d'action du ".$extraction->getDateAction()->format('d/m/Y H:i');
			$this->getMailer()->sendExportAction($extraction->getUtilisateur()->getEmail(), $sub, $filename);
			$extraction->setEtat(1);
			$em->persist($extraction);
		}
		$em->flush();
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
	
}
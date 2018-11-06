<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Extraction;

class ExportCommand extends BaseCommand {
	
	protected function configure(){
			parent::configure();
			$this->setName('super:export:action')->setDescription("envoi des extractions d'action automatisé");
	}
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$extractions = $em->getRepository('OrangeMainBundle:Extraction')->findByEtat(0);
		$mailer = $this->getContainer()->get('mailer');
		$spool = $mailer->getTransport()->getSpool();
		$transport = $this->getContainer()->get('swiftmailer.transport.real');
		foreach($extractions as $extraction) {
			switch($extraction->getType()) {
				case Extraction::$types['action']:
					$this->extractionAction($extraction);
					break;
				case Extraction::$types['occurence']:
					$this->extractionOccurrence($extraction);
					break;
				case Extraction::$types['notification']:
					$this->extractionNotification($extraction);
					break;
				case Extraction::$types['signalisation']:
					$this->extractionSignalisation($extraction);
					break;
			}
			//$extraction->setEtat(1);
			$this->getEntityManager()->persist($extraction);
			$spool->flushQueue($transport);
		}
		$em->flush();
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
	
	/**
	 * @param Extraction $extraction
	 */
	public function extractionAction(Extraction $extraction) {
		$statut = $this->getEntityManager()->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$query = $this->getEntityManager()->createQuery($extraction->getQuery());
		$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
		$query->setParameters(unserialize($extraction->getParam()));
		$actions       = $query->getArrayResult();
		$objWriter = $this->get('orange.main.extraction')->exportAction($actions, $statut->getQuery()->execute());
		$filename = 'actions_'.date("Y-m-d_H-i-s").'.xlsx';
		$objWriter->save("./web/upload/reporting/$filename");
		$sub = "Extraction d'action du ".$extraction->getDateAction()->format('d/m/Y H:i');
		$this->getMailer()->sendExtraction($extraction->getUtilisateur()->getEmail(), $sub, $filename);
	}
	
	/**
	 * @param Extraction $extraction
	 */
	public function extractionOccurrence(Extraction $extraction) {
		$query = $this->getEntityManager()->createQuery($extraction->getQuery());
		$query->setParameters(unserialize($extraction->getParam()));
		$statut = $this->getEntityManager()->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
		$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
		$objWriter     = $this->get('orange.main.extraction')->exportOccurence($query->getArrayResult(), $statut->getQuery()->execute());
		$filename = 'occurences_'.date("Y-m-d_H-i-s").'.xlsx';
		$objWriter->save("./web/upload/reporting/$filename");
		$sub = "Extraction d'occurence du ".$extraction->getDateAction()->format('d/m/Y H:i');
		$this->getMailer()->sendExtraction($extraction->getUtilisateur()->getEmail(), $sub, $filename);
	}
	
	/**
	 * @param Extraction $extraction
	 */
	public function extractionNotification(Extraction $extraction) {
		$query = $this->getEntityManager()->createQuery($extraction->getQuery());
		$query->setParameters(unserialize($extraction->getParam()));
		$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
		$objWriter     = $this->get('orange.main.extraction')->exportNotification($query->getResult());
		$filename = 'notifications_'.date("Y-m-d_H-i-s").'.xlsx';
		$objWriter->save("./web/upload/reporting/$filename");
		$sub = "Extraction de notification du ".$extraction->getDateAction()->format('d/m/Y H:i');
		$this->getMailer()->sendExtraction($extraction->getUtilisateur()->getEmail(), $sub, $filename);
	}
	
	/**
	 * @param Extraction $extraction
	 */
	public function extractionSignalisation(Extraction $extraction) {
		$statut = $this->getEntityManager()->getRepository('OrangeMainBundle:Statut')->listAllStatutSign();
		$query = $this->getEntityManager()->createQuery($extraction->getQuery());
		//$query->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD, 1);
		$query->setParameters(unserialize($extraction->getParam()));
		$data = $this->getMapping()->getExtraction()->exportSignalisation($query->execute(), $statut->getQuery()->execute());
		$objWriter = $this->get('orange.main.extraction')->exportSignalisation($data);
		$filename = 'signalisations_'.date("Y-m-d_H-i-s").'.xlsx';
		$objWriter->save("./web/upload/reporting/$filename");
		$sub = "Extraction de signalisation du ".$extraction->getDateAction()->format('d/m/Y H:i');
		$this->getMailer()->sendExtraction($extraction->getUtilisateur()->getEmail(), $sub, $filename);
	}
	
}
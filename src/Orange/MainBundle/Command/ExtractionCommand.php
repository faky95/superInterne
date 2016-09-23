<?php

namespace Orange\MainBundle\Command;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractionCommand extends BaseCommand {
	
	
	protected function configure(){
			parent::configure();
			$this->setName($this->getName() . ':extraction')
				->setDescription('envoi des reporting automatisé');
		
	}
	
	
	
	public function execute(InputInterface $input, OutputInterface $output){
		$em = $this->getEntityManager();
		$envois = $em->getRepository('OrangeMainBundle:Envoi')->getEnvoi();
		$pas = $em->getRepository('OrangeMainBundle:Pas')->listAllPas()->getQuery()->execute();
		$per = array();
		foreach ($pas as $value){
			$per[$value->getId()] = $value->getLibelle();			
		}
		$dest = array('aziz' => 'abdouaziz.ndaw@orange-sonatel.com');
		foreach ($envois as $envoi){
			$statut = $em->getRepository('OrangeMainBundle:Statut')->listAllStatuts();
			$query = $this->getEntityManager()->createQuery($envoi->getReporting()->getRequete());
			$query->setParameters(unserialize($envoi->getReporting()->getParameter()));
			exit('v,kl,smc');
			$data = $this->get('orange.main.mapping')->exportAction($query->execute(), $statut->getQuery()->execute());
			$objWriter = $this->get('orange.main.extraction')->exportAction($data);
			$filename = 'reporting_automatise_'.date("Y-m-d_H-i").'.xlsx';
			$objWriter->save("./web/upload/reporting/$filename");
			$i=0;
			foreach ($envoi->getReporting()->getDestinataire() as $destinataire){
				$dest[$i] = $destinataire->getEmail();
				$i++;
			}
			$sub = "Reporting ".$per[$envoi->getReporting()->getPas()->getId()];
			$result = $this->getMailer()->sendReport($dest, $sub, $filename);
		}
		
		
		
		$to = array('abdouaziz.ndaw@orange-sonatel.com');
		$subject = "ceci est un test.";
		$filename = 'reporting_automatise_'.date("Y-m-d_H-i").'xlsx';
		
			
		$output->writeln(utf8_encode('Yes! ça marche'));
	}
	
}
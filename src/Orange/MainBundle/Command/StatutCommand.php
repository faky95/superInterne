<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Command\BaseCommand;
use Orange\MainBundle\Utils\ActionUtils;

class StatutCommand extends BaseCommand
{
	
	protected function configure() {
		parent::configure();
		$this->setName('statut:changement')->setDescription('Changement de statut');
	}
	
	public function execute(InputInterface $input, OutputInterface $output) {
		$em = $this->getEntityManager();
		$today = new \DateTime();
		$today = $today->format('Y-m-d H:i:s');
		$connexion = $this->get('database_connection');
		$statut_row = array(
				'statut_id' => 0, 'utilisateur_id' => 1, 'dateStatut' => $today, 'commentaire' => 'Action échue non soldée'
			);
		$tacheEchue = $em->getRepository('OrangeMainBundle:Tache')->tacheEchue();
		$actionEchue = $em->getRepository('OrangeMainBundle:Action')->actionEchue();
		$actionNonEchue = $em->getRepository('OrangeMainBundle:Action')->actionNonEchue();
		foreach($tacheEchue as $tache) {
			if($tache->getEtatCourant() !== Statut::ACTION_ECHUE_NON_SOLDEE) {
				$statut_row['tache_id'] 	  			= $tache->getId();
				$statut_row['statut_id'] 	  			= $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_ECHUE_NON_SOLDEE)->getId();
				$statut_row['dateStatut'] 				= date('Y-m-d H:i:s');
				$statut_row['commentaire'] 				= 'Tache échue non soldée';
				$connexion->insert('tache_has_statut', $statut_row);
				$tache->setEtatCourant(Statut::ACTION_ECHUE_NON_SOLDEE);
				$em->persist($tache);
				$em->flush();
			}
		}
		foreach($actionEchue as $action) {
			if($action->getEtatCourant() !== Statut::ACTION_ECHUE_NON_SOLDEE && $action->getEtatReel() !== Statut::ACTION_ECHUE_NON_SOLDEE) {
				$statut_row['action_id'] 	  			= $action->getId();
				$statut_row['statut_id'] 	  			= $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_ECHUE_NON_SOLDEE)->getId();
				$statut_row['dateStatut'] 				= date('Y-m-d H:i:s');
				$statut_row['commentaire'] 				= 'Action échue non soldée';
				$connexion->insert('action_has_statut', $statut_row);
				ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_ECHUE_NON_SOLDEE, Statut::ACTION_ECHUE_NON_SOLDEE);
			}
		}
		foreach($actionNonEchue as $action) {
			if($action->getEtatCourant() !== Statut::ACTION_NON_ECHUE && $action->getEtatReel() !== Statut::ACTION_NON_ECHUE) {
				$statut_row['action_id'] 	  			= $action->getId();
				$statut_row['statut_id'] 	  			= $em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_NON_ECHUE)->getId();
				$statut_row['dateStatut'] 				= date('Y-m-d H:i:s');
				$statut_row['commentaire'] 				= 'Action non échue';
				$connexion->insert('action_has_statut', $statut_row);
				ActionUtils::updateEtatCourantEntity($em, $action, Statut::ACTION_NON_ECHUE, Statut::ACTION_NON_ECHUE);
			}
		}
		$output->writeln(utf8_encode('Commande exécutée avec succès !'));
	}
	
}

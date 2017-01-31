<?php
namespace Orange\MainBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Command\BaseCommand;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Utils\SignalisationUtils;

class SignalisationCommand extends BaseCommand
{
	
	protected function configure() {
		parent::configure();
		$this->setName('signalisation:prisecharge')
			 ->setDescription('Prise en charge de la signalisation.');
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getEntityManager();
		$today = new \DateTime();
		$today = $today->format('Y-m-d');
		
		$signalisation_array = $em->getRepository('OrangeMainBundle:Signalisation')->findByEtatCourant(Statut::NOUVELLE_SIGNALISATION);
		foreach ($signalisation_array as $one_signalisation) {
			$dateSignale = $one_signalisation->getDateSignale()->format('Y-m-d');
			$difference = ActionUtils::dateDiff($today, $dateSignale);
			$difference = (int)$difference["day"];
			if($difference > 2) {
				$animateurs = $one_signalisation->getInstance()->getAnimateur();
				if(count($animateurs) > 1) {
					$countSignalisation = array();
					foreach ($animateurs as $anim) {
						$countSignalisation[$anim->getId()] = count($anim->getUtilisateur()->getSignalisation());
						array_push($countSignalisation, $countSignalisation[$anim->getId()]);
					}
					$animateur_id = array_search(min($countSignalisation), $countSignalisation);
				} else {
					$animateur_id = $animateurs[0]->getId();
				}
				$animateurUser = $em->getRepository('OrangeMainBundle:Animateur')->find($animateur_id)->getUtilisateur();
				SignalisationUtils::addAnimateur ($em, $animateurUser, $one_signalisation);
// 				Notifier l'animateur de cette signalisation et toutes les parties concernées.
				$one_signalisation->setEtatCourant(Statut::SIGNALISATION_PRISE_CHARGE);
				$em->persist($one_signalisation);
				$em->flush();
			}
		}
		$output->writeln(utf8_encode('Commande exécutée avec succès !'));
	}
	
}

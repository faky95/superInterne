<?php
namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;
use Orange\MainBundle\Entity\Envoi;

class Envois{
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	protected $pas;
	
	public function __construct($pas, $em)
	{
		$this->em = $em;
		$this->pas = $pas;
	
	}
	
	public function generateEnvoi($entity) {
		$date = date('Y-01-01');
		$date = strtotime($date);
		$envoi = new Envoi();
		$periodicite = $entity->getPas()->getId();
		$type = $entity->getTypeReporting();
		$semaine = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday','saturday', 'sunday' );
		$num = array('first', 'second', 'third', 'fourth', 'fifth', 'sixth');
		if($periodicite == $this->pas['Journaliere']){
			$dEnvoi = date('Y-m-d');
			$dEnvoi = strtotime($dEnvoi);
			$dateEnvoi = date('Y-m-d', strtotime('+1 day', $dEnvoi));
			$envoi->setPeriodicite(1);
			$envoi->setTypeReporting($type);
			$envoi->setDateEnvoi(new \DateTime($dateEnvoi));
			$entity->addEnvoi($envoi);
			$envoi->setReporting($entity);
		}
		elseif ($periodicite == $this->pas['Hebdomadaire']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$jours = $semaine[$numJr];
			$dateEnvoi = date('Y-m-d', strtotime($jours.' this week'));
			$envoi->setPeriodicite(1);
			$envoi->setTypeReporting($type);
			$envoi->setDateEnvoi(new \DateTime($dateEnvoi));
			$entity->addEnvoi($envoi);
			$envoi->setReporting($entity);
		}
		elseif ($periodicite == $this->pas['Quinzaine']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$it = $entity->getIteration();
			$jours = $semaine[$numJr];
			$dateEnvoi = date('Y-m-d', strtotime($jours.' this week'));
			$dateEnvoi = strtotime($dateEnvoi);
			if ($it == 1){
				$dateEnvoi = date('Y-m-d', strtotime('+1 week', $dateEnvoi));
			}else {
				$dateEnvoi = date('Y-m-d', strtotime('+2 week', $dateEnvoi));
			}
			$envoi->setDateEnvoi(new \DateTime($dateEnvoi));
			$entity->addEnvoi($envoi);
			$envoi->setReporting($entity);
			$envoi->setTypeReporting($type);
		}
		elseif ($periodicite == $this->pas['Mensuelle']){
			$numJr = $entity->getDayOfMonth()->getValeur();
			if ($numJr < date('j')){
				$dateEnvoi = date('Y-m-'.$numJr, strtotime('+1 month'));
			}
			else
				$dateEnvoi = date('Y-m-'.$numJr, strtotime('this month'));
			$envoi->setDateEnvoi(new \DateTime($dateEnvoi));
			$entity->addEnvoi($envoi);
			$envoi->setReporting($entity);
			$envoi->setTypeReporting($type);
		}
		elseif ($periodicite == $this->pas['Bimestrielle']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$it = $entity->getIteration();
			$jours = $semaine[$numJr];
			$n = $num[$it-1];
			$dateEnvoi = date('Y-m-d', strtotime( $n.' '.$jours, $date));
			$pas=2;
			for($i=0; $i<6; $i++){
				$d = date('Y-m-d', strtotime("+".($i * $pas)." month",strtotime($dateEnvoi)));
				$envoi->setDateEnvoi(new \DateTime($d));
				$envoi->setPeriodicite(4);
				$entity->addEnvoi($envoi);
				$envoi->setTypeReporting($type);
				$envoi->setReporting($entity);
			}
		}elseif ($periodicite == $this->pas['Trimestrielle']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$it = $entity->getIteration();
			$jours = $semaine[$numJr];
			$n = $num[$it-1];
			$dateEnvoi = date('Y-m-d', strtotime( $n.' '.$jours, $date));
			$pas=3;
			for($i=0; $i<4; $i++){
				$d = date('Y-m-d', strtotime("+".($i * $pas)." month",strtotime($dateEnvoi)));
				$envoi->setDateEnvoi(new \DateTime($d));
				$envoi->setPeriodicite(5);
				$envoi->setTypeReporting($type);
				$entity->addEnvoi($envoi);
				$envoi->setReporting($entity);
			}
		}elseif ($periodicite == $this->pas['Quadrimestrielle']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$it = $entity->getIteration();
			$jours = $semaine[$numJr];
			$n = $num[$it-1];
			$dateEnvoi = date('Y-m-d', strtotime( $n.' '.$jours, $date));
			$pas=4;
			for($i=0; $i<3; $i++){
				$d = date('Y-m-d', strtotime("+".($i * $pas)." month",strtotime($dateEnvoi)));
				$envoi->setDateEnvoi(new \DateTime($d));
				$envoi->setPeriodicite(6);
				$entity->addEnvoi($envoi);
				$envoi->setTypeReporting($type);
				$envoi->setReporting($entity);
			}
		}
		elseif ($periodicite == $this->pas['Semestrielle']){
			$numJr = $entity->getDayOfWeek()->getValeur();
			$it = $entity->getIteration();
			$jours = $semaine[$numJr];
			$n = $num[$it-1];
			$dateEnvoi = date('Y-m-d', strtotime( $n.' '.$jours, $date));
			$pas=6;
			for($i=0; $i<2; $i++){
				$d = date('Y-m-d', strtotime("+".($i * $pas)." month",strtotime($dateEnvoi)));
				$envoi->setDateEnvoi(new \DateTime($d));
				$envoi->setPeriodicite(7);
				$entity->addEnvoi($envoi);
				$envoi->setTypeReporting($type);
				$envoi->setReporting($entity);
			}
		}
		
	}
}
	
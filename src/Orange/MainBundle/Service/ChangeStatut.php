<?php
namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Entity\ActionStatut;

class ChangeStatut{
	
	public function __construct($em)
	{
		$this->em = $em;
	}
	
	public function ChangeStatutAction($action, $user){
		$today = new \DateTime();
		$code = $action->getStatutChange()->getCode();
		$statut = new ActionStatut();
		if($code === Statut::ACTION_NON_ECHUE){
			if($today > $action->getDateInitial()){
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_ECHUE_NON_SOLDEE);
				$action->setEtatCourant('ACTION_ECHUE_NON_SOLDEE');
				$action->setEtatReel('ACTION_ECHUE_NON_SOLDEE');
			}else{ 
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_NON_ECHUE);
				$action->setEtatCourant('ACTION_NON_ECHUE');
				$action->setEtatReel('ACTION_NON_ECHUE');
			}
		}
		elseif($code === Statut::ACTION_FAIT_DELAI){
			if($action->getDateFinExecut()->format('Y-m-d') > $action->getDateInitial()->format('Y-m-d')){
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_HORS_DELAI);
				$action->setEtatCourant('ACTION_FAIT_HORS_DELAI');
				$action->setEtatReel('ACTION_FAIT_HORS_DELAI');
			}else{ 
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_FAIT_DELAI);
				$action->setEtatCourant('ACTION_FAIT_DELAI');
				$action->setEtatReel('ACTION_FAIT_DELAI');
			}
		}elseif($code === Statut::ACTION_SOLDEE_DELAI){
			if($action->getDateFinExecut() > $action->getDateInitial()){
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_HORS_DELAI);
				$action->setEtatCourant('ACTION_SOLDEE_HORS_DELAI');
				$action->setEtatReel('ACTION_SOLDEE_HORS_DELAI');
			}else{
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_SOLDEE_DELAI);
				$action->setEtatCourant('ACTION_SOLDEE_DELAI');
				$action->setEtatReel('ACTION_SOLDEE_DELAI');
			}
			$action->setDateCloture(new \DateTime("now"));
		}elseif($code === Statut::ACTION_ABANDONNEE){
				$st = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode(Statut::ACTION_ABANDONNEE);
				$action->setEtatCourant('ACTION_ABANDONNEE');
				$action->setEtatReel('ACTION_ABANDONNEE');
		}
	
		$statut->setUtilisateur($user);
		$statut->setStatut($st);
		$statut->setAction($action);
		$this->em->persist($statut);
		$this->em->persist($action);
		$this->em->flush();
	}
	
}
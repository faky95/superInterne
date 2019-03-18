<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Validator\Constraints\CycliquePeriodiciteDate as CPAssert;

/**
 * ActionCyclique
 * @ORM\Table(name="action_cyclique")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionCycliqueRepository")
 * @CPAssert
 */
class ActionCyclique
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var Action
     * @ORM\OneToOne(targetEntity="Action", inversedBy="actionCyclique", cascade={"persist"})
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     **/
    private $action;
    
    /**
     * @var number
     * @ORM\Column(name="occurence", type="integer", nullable=false)
     */
    private $occurence = 0;
    
    /**
     * @ORM\ManyToOne(targetEntity="Orange\MainBundle\Entity\Pas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pas_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Vous devez chosir la périodicité")
     */
    private $pas;
    
    /**
     * @ORM\OneToMany(targetEntity="Tache", mappedBy="actionCyclique", cascade={"persist", "merge", "remove"})
     **/
    private $tache;
    
    /**
     * @var DayOfMonth
     * @ORM\ManyToOne(targetEntity="DayOfMonth")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_month_id", referencedColumnName="id")
     * })
     */
    private $dayOfMonth;
    
    /**
     * @var DayOfWeek
     * @ORM\ManyToOne(targetEntity="DayOfWeek")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_week_id", referencedColumnName="id")
     * })
     */
    private $dayOfWeek;
    
    /**
     * @var integer
     * @ORM\Column(name="iteration", type="integer", nullable=true)
     */
    private $iteration;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tache = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
	 * @return number
	 */
	public function getOccurence() {
		return $this->ocurence;
	}
	
	/**
	 * @param $occurence
	 * @return ActionCyclique
	 */
	public function setOccurence($occurence) {
		$this->ocurence = $occurence;
		return $this;
	}

    /**
     * Set action
     * @param \Orange\MainBundle\Entity\Action $action
     * @return ActionCyclique
     */
    public function setAction(\Orange\MainBundle\Entity\Action $action = null)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get action
     * @return \Orange\MainBundle\Entity\Action 
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * new tache
     * @param array $arrPas
     * @return Tache
     */
    public function newTache($arrPas) {
    	$tache = new Tache();
    	$tache->setActionCyclique($this);
    	$this->occurence = $this->getTache()->count() ? $this->getTache()->count() + 1 : 1;
    	$pas = $this->pas->getId();
    	$it = $this->getIteration();
    	$numJr = $this->getDayOfWeek() ? $this->getDayOfWeek()->getValeur() : null;
    	$num = array('first', 'second', 'third', 'fourth', 'fifth', 'sixth');
    	$semaine = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday','saturday', 'sunday');
    	if($pas == $arrPas['Hebdomadaire']) {
    		$dateDebut = date('Y-m-d', strtotime('monday this week'));
    		$dateInitial = $numJr ? date('Y-m-d', strtotime($semaine[$numJr].' this week')) : $dateDebut;
    		$dateFin = date('Y-m-d', strtotime('sunday this week'));
    		$tache->setReference(sprintf('%s_H%02d_%s', $this->action->getReference(), date('W'), date('Y')));
    	} elseif($pas == $arrPas['Quinzaine']) {
    		$dateDebut = (date('d') < 16) ? date('Y-m-01') : date('Y-m-16');
    		$dateInitial = date('Y-m-d', strtotime('next '.$semaine[$numJr], strtotime($dateDebut)));
    		$dateInitial = date('Y-m-d', strtotime('+'.($it - 1).' week', strtotime($dateInitial)));
    		$dateFin = (date('d') < 16) ? date('Y-m-15') : date('Y-m-d', strtotime('last day of this month'));
    		$tache->setReference(sprintf('%s_Q%s_%s_%s', $this->action->getReference(), $this->occurence, date('m'), date('Y')));
    	} elseif($pas == $arrPas['Mensuelle'] && $this->getDayOfMonth()) {
    		$numJr = $this->getDayOfMonth()->getValeur();
    		$dateDebut= date('Y-m-d', strtotime('first day of this month'));
    		$dateInitial = date('Y-m-'.$numJr);
    		$dateFin = date('Y-m-d', strtotime('last day of this month'));
    		$tache->setReference(sprintf('%s_%s_%s', $this->action->getReference(), date('m'), date('Y')));
    	} elseif($pas == $arrPas['Trimestrielle']) {
    		$numJr = $this->getDayOfMonth()->getValeur();
    		$dateDebut = date(sprintf('Y-%s-01', (floor(date('m') / 3) * 3) + 1));
    		$dateInitial= date('Y-m-d', strtotime(sprintf("+%s day", $numJr - 1), strtotime(sprintf('+%s month', $it-1), strtotime($dateDebut))));
    		$dateFin = date('Y-m-d', strtotime('-1 day', strtotime('+3 month', strtotime($dateDebut))));
    		$tache->setReference(sprintf('%s_T%s_%s', $this->action->getReference(), ceil(date('m')/3), date('Y')));
    	} elseif($pas == $arrPas['Semestrielle']) {
    		$numJr = $this->getDayOfMonth()->getValeur();
    		$dateDebut = date(sprintf('Y-%s-01', (floor(date('m') / 6) * 6) + 1));
    		$dateInitial= date('Y-m-d', strtotime(sprintf("+%s day", $numJr - 1), strtotime(sprintf('+%s month', $it-1), strtotime($dateDebut))));
    		$dateFin = date('Y-m-d', strtotime('-1 day', strtotime('+6 month', strtotime($dateDebut))));
    		$tache->setReference(sprintf('%s_S%s_%s', $this->action->getReference(), ceil(date('m')/6), date('Y')));
    	}
    	$tache->setDateDebut(new \DateTime($dateDebut));
    	$tache->setDateInitial(new \DateTime($dateInitial));
        $tache->setDateFin(new \DateTime($dateFin));
       // $tache->setNumeroTache($this->occurence);
    	if(!isset($dateInitial) || !isset($dateDebut) || $this->action->getDateDebut() > $tache->getDateDebut()) {
    		return null;
    	}
    	$this->addTache($tache);
    	return $tache;
    }

    /**
     * Add tache
     * @param \Orange\MainBundle\Entity\Tache $tache
     * @return ActionCyclique
     */
    public function addTache(\Orange\MainBundle\Entity\Tache $tache)
    {
    	$tache->setActionCyclique($this);
        $this->tache[] = $tache;
        return $this;
    }

    /**
     * Remove tache
     * @param \Orange\MainBundle\Entity\Tache $tache
     */
    public function removeTache(\Orange\MainBundle\Entity\Tache $tache)
    {
        $this->tache->removeElement($tache);
    }

    /**
     * Get tache
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTache()
    {
        return $this->tache;
    }
    
    /**
     * 
     * @return string
     */
    public function getLibelle() {
    	return $this->getAction() ? $this->getAction()->getLibelle() : 'non renseigné';
    }
    
    /**
     * @return NULL|\Orange\MainBundle\Entity\Instance
     */
    public function getInstance() {
    	return $this->getAction() ? $this->getAction()->getInstance() : null;
    }
    
    /**
     * @return NULL|\Orange\MainBundle\Entity\Utilisateur
     */
    public function getPorteur(){
    	return $this->getAction()?$this->getAction()->getPorteur():null;
    }

    /**
     * Set pas
     * @param \Orange\MainBundle\Entity\Pas $pas
     * @return ActionCyclique
     */
    public function setPas(\Orange\MainBundle\Entity\Pas $pas = null)
    {
        $this->pas = $pas;
        return $this;
    }

    /**
     * Get pas
     * @return \Orange\MainBundle\Entity\Pas
     */
    public function getPas()
    {
        return $this->pas;
    }

    /**
     * Set dayOfMonth
     * @param \Orange\MainBundle\Entity\DayOfMonth $dayOfMonth
     * @return ActionCyclique
     */
    public function setDayOfMonth(\Orange\MainBundle\Entity\DayOfMonth $dayOfMonth = null)
    {
        $this->dayOfMonth = $dayOfMonth;
        return $this;
    }

    /**
     * Get dayOfMonth
     * @return \Orange\MainBundle\Entity\DayOfMonth
     */
    public function getDayOfMonth()
    {
        return $this->dayOfMonth;
    }

    /**
     * Set dayOfWeek
     * @param \Orange\MainBundle\Entity\DayOfWeek $dayOfWeek
     * @return ActionCyclique
     */
    public function setDayOfWeek(\Orange\MainBundle\Entity\DayOfWeek $dayOfWeek = null)
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    /**
     * Get dayOfWeek
     * @return \Orange\MainBundle\Entity\DayOfWeek
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Set iteration
     * @param integer $iteration
     * @return ActionCyclique
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;
        return $this;
    }

    /**
     * Get iteration
     * @return integer
     */
    public function getIteration()
    {
        return $this->iteration;
    }
    
    /**
     * get echeance occurence
     * @return string
     */
    public function echeanceOccurence() {
    	$echeance = null;
    	if($this->pas->getPeriodicite()->getId()==Periodicite::$ids['hebdomadaire']) {
    		if($this->iteration==null) {
    			$echeance = sprintf('chaque %s', $this->dayOfWeek);
    		} else {
    			$echeance = sprintf('le %s %s de chaque quinzaine', $this->iteration.'e', $this->dayOfWeek);
    		}
    	} elseif($this->pas->getPeriodicite()->getId()==Periodicite::$ids['mensuelle']) {
    		if($this->iteration==null) {
    			$echeance = sprintf('chaque %s du mois', $this->dayOfMonth->getValeur());
    		} else {
    			$echeance = sprintf('le %s %s du %s', $this->iteration.'e', $this->dayOfMonth->getValeur(), $this->pas->getChaine());
    		}
    	}
    	return $echeance;
    }
}

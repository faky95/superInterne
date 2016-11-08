<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Validator\Constraints\CycliquePeriodiciteDate as CPAssert;

/**
 * ActionCyclique
 *
 * @ORM\Table(name="action_cyclique")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionCycliqueRepository")
 * @CPAssert
 */
class ActionCyclique
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="actionCyclique", cascade={"persist"})
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     **/
    private $action;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Orange\MainBundle\Entity\Pas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pas_id", referencedColumnName="id")
     * })
     */
    private $pas;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Tache", mappedBy="actionCyclique", cascade={"persist","merge"})
     **/
    private $tache;
    
    /**
     * @var \DayOfMonth
     *
     * @ORM\ManyToOne(targetEntity="DayOfMonth")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_month_id", referencedColumnName="id")
     * })
     *
     *
     */
    private $dayOfMonth;
    
    /**
     * @var \DayOfWeek
     *
     * @ORM\ManyToOne(targetEntity="DayOfWeek")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_week_id", referencedColumnName="id")
     * })
     *
     *
     */
    private $dayOfWeek;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="iteration", type="integer", nullable=true)
     *
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
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     *
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
     *
     * @return \Orange\MainBundle\Entity\Action 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Add tache
     *
     * @param \Orange\MainBundle\Entity\Tache $tache
     * @return ActionCyclique
     */
    public function addTache(\Orange\MainBundle\Entity\Tache $tache)
    {
        $this->tache[] = $tache;

        return $this;
    }

    /**
     * Remove tache
     *
     * @param \Orange\MainBundle\Entity\Tache $tache
     */
    public function removeTache(\Orange\MainBundle\Entity\Tache $tache)
    {
        $this->tache->removeElement($tache);
    }

    /**
     * Get tache
     *
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
    public function getLibelle(){
    	return $this->getAction()?$this->getAction()->getLibelle():'non renseigné';
    }
    
    /**
     * 
     * @return NULL|\Orange\MainBundle\Entity\Instance
     */
    public function getInstance(){
    	return $this->getAction()?$this->getAction()->getInstance():null;
    }
    
    /**
     * 
     * @return NULL|\Orange\MainBundle\Entity\Utilisateur
     */
    public function getPorteur(){
    	return $this->getAction()?$this->getAction()->getPorteur():null;
    }


    /**
     * Set pas
     *
     * @param \Orange\MainBundle\Entity\Pas $pas
     *
     * @return ActionCyclique
     */
    public function setPas(\Orange\MainBundle\Entity\Pas $pas = null)
    {
        $this->pas = $pas;

        return $this;
    }

    /**
     * Get pas
     *
     * @return \Orange\MainBundle\Entity\Pas
     */
    public function getPas()
    {
        return $this->pas;
    }

    /**
     * Set dayOfMonth
     *
     * @param \Orange\MainBundle\Entity\DayOfMonth $dayOfMonth
     *
     * @return ActionCyclique
     */
    public function setDayOfMonth(\Orange\MainBundle\Entity\DayOfMonth $dayOfMonth = null)
    {
        $this->dayOfMonth = $dayOfMonth;

        return $this;
    }

    /**
     * Get dayOfMonth
     *
     * @return \Orange\MainBundle\Entity\DayOfMonth
     */
    public function getDayOfMonth()
    {
        return $this->dayOfMonth;
    }

    /**
     * Set dayOfWeek
     *
     * @param \Orange\MainBundle\Entity\DayOfWeek $dayOfWeek
     *
     * @return ActionCyclique
     */
    public function setDayOfWeek(\Orange\MainBundle\Entity\DayOfWeek $dayOfWeek = null)
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * Get dayOfWeek
     *
     * @return \Orange\MainBundle\Entity\DayOfWeek
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Set iteration
     *
     * @param integer $iteration
     *
     * @return ActionCyclique
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * Get iteration
     *
     * @return integer
     */
    public function getIteration()
    {
        return $this->iteration;
    }
}

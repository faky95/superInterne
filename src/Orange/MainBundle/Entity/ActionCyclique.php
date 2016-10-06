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
	
	const PERIODICITE_HEBDOMADAIRE 			= 'HEBDOMADAIRE';
	const PERIODICITE_MENSUEL				= 'MENSUEL';
	const PERIODICITE_BIMESTRIEL			= 'BIMESTRE';
	const PERIODICITE_TRIMESTRIEL			= 'TRIMESTRE';
	const PERIODICITE_SEMESTRIEL			= 'SEMESTRE';
	const PERIODICITE_ANNUEL				= 'ANNUEL';
	
	
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
     * @var string
     *
     * @ORM\Column(name="periodicite", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $periodicite;
    
    /**
     * @ORM\OneToMany(targetEntity="Tache", mappedBy="actionCyclique", cascade={"persist","merge"})
     **/
    private $tache;
    
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
     * Set periodicite
     *
     * @param string $periodicite
     * @return ActionCyclique
     */
    public function setPeriodicite($periodicite)
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    /**
     * Get periodicite
     *
     * @return string 
     */
    public function getPeriodicite()
    {
        return $this->periodicite;
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
}

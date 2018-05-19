<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Orange\MainBundle\Validator\Constraints\CycliquePeriodiciteDate as CPAssert;

/**
 * Reco
 * @ORM\Table(name="reco")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\RecoRepository")
 * @CPAssert
 */
class Reco
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
	 * @ORM\OneToOne(targetEntity="Action", inversedBy="reco", cascade={"persist", "merge", "remove"})
	 * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
	 **/
	private $action;
	
	/**
	 * @var Processus
	 * @ORM\ManyToOne(targetEntity="Processus")
	 * @ORM\JoinColumn(name="processus_id", referencedColumnName="id")
	 **/
	private $processus;
	
	/**
	 * @var Evenement
	 * @ORM\ManyToOne(targetEntity="Evenement")
	 * @ORM\JoinColumn(name="evenement_id", referencedColumnName="id")
	 **/
	private $evenement;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumn(name="coordonateur_arq", referencedColumnName="id")
	 **/
	private $coordonnateurARQ;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumn(name="coordonateur_aqcq", referencedColumnName="id")
	 **/
	private $coordonnateurAQCQ;

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Reco
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
     * Set processus
     * @param \Orange\MainBundle\Entity\Processus $processus
     * @return Reco
     */
    public function setProcessus(\Orange\MainBundle\Entity\Processus $processus= null)
    {
    	$this->processus = $processus;
    	return $this;
    }
    
    /**
     * Get processus
     * @return \Orange\MainBundle\Entity\Processus
     */
    public function getProcessus()
    {
    	return $this->processus;
    }
    
    /**
     * Set evenement
     * @param \Orange\MainBundle\Entity\Evenement $evenement
     * @return Reco
     */
    public function setEvenement(\Orange\MainBundle\Entity\Evenement $evenement = null)
    {
    	$this->evenement = $evenement;
    	return $this;
    }
    
    /**
     * Get evenement
     * @return \Orange\MainBundle\Entity\Evenement
     */
    public function getEvenement()
    {
    	return $this->evenement;
    }
    
    /**
     * Set coordonateurARQ
     * @param \Orange\MainBundle\Entity\Utilisateur $coordonateurARQ
     * @return Reco
     */
    public function setCoordonateurARQ(\Orange\MainBundle\Entity\Utilisateur $coordonateurARQ = null)
    {
    	$this->coordonateurARQ = $coordonateurARQ;
    	return $this;
    }
    
    /**
     * Get coordonateurARQ
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getCoordonateurARQ()
    {
    	return $this->coordonateurARQ;
    }
    
    /**
     * Set coordonateurAQCQ
     * @param \Orange\MainBundle\Entity\Utilisateur $coordonateurAQCQ
     * @return Reco
     */
    public function setCoordonateurAQCQ(\Orange\MainBundle\Entity\Utilisateur $coordonateurAQCQ= null)
    {
    	$this->coordonateurAQCQ= $coordonateurAQCQ;
    	return $this;
    }
    
    /**
     * Get coordonateurAQCQ
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getCoordonateurAQCQ()
    {
    	return $this->coordonateurAQCQ;
    }
    
}

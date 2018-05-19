<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evenement
 * @ORM\Table(name="evenement")
 * @ORM\Entity
 */
class Evenement
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * @var TypeAction
	 * @ORM\OneToOne(targetEntity="TypeAction")
	 * @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
	 **/
	private $typeAction;
	
	/**
	 * @var Processus
	 * @ORM\ManyToOne(targetEntity="Processus")
	 * @ORM\JoinColumn(name="processus_id", referencedColumnName="id")
	 **/
	private $processus;
	
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
     * Set typeAction
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Reco
     */
    public function setTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction= null)
    {
    	$this->typeAction = $typeAction;
        return $this;
    }

    /**
     * Get typeAction
     * @return \Orange\MainBundle\Entity\TypeAction 
     */
    public function getTypeAction()
    {
        return $this->typeAction;
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

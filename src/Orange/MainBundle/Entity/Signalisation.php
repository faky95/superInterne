<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Validator\Constraints\SignalisationDate as SIAssert;

/**
 * Signalisation
 *
 * @ORM\Table(name="signalisation")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\SignalisationRepository")
 * @SIAssert
 */
class Signalisation
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     * @Assert\NotBlank()
     * 
     */
	private $libelle;
        
       /**
	 * @var string
	 *
	 * @ORM\Column(name="site", type="string", length=45, nullable=true)
	 * @Assert\NotBlank()
	 *
	 */
	private $site;
	
	
	/**
	 *
	 * @var String
	 * 
	 * @ORM\Column(name="reference", type="string", length=45, nullable=true)
     * 
	 */
	private $reference;
	
	/**
	 * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank()
     * 
	 */
	private $description;
	
	/**
	 * @ORM\OneToMany(targetEntity="SignalisationStatut", mappedBy="signalisation", cascade={"persist","remove","merge"})
	 */
	private $signStatut;
	
	/**
	 * @ORM\OneToMany(targetEntity="SignalisationAnimateur", mappedBy="signalisation", cascade={"persist","remove","merge"})
	 */
	private $signalisationAnimateur;
	
	/**
	 * @var \Animateur
	 * @ORM\ManyToOne(targetEntity="Instance")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 * })
	 * @Assert\NotBlank(message="Vous devez choisir l'instance ! ")
	 */
	private $instance;
	
	/**
	 * @var \Source
	 *
	 * @ORM\ManyToOne(targetEntity="Source")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="source_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $source;
	
	/**
	 * @var \Orange\MainBundle\Entity\Instance
	 */
	public $perimetre;
	
	/**
	 * @var \Orange\MainBundle\Entity\Domaine
	 */
	public $dom;
	
	/**
	 * @var \Orange\MainBundle\Entity\TypeAction
	 */
	public $type;
	
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	public $constat;
	
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	public $utilisateur;
	
	public $statut;
	
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Action", mappedBy="signalisation")
	 */
	private $action;
	
	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="constatateur", referencedColumnName="id")
	 * })
	 *
	 * @Assert\NotBlank(message="Choisissez l'agent ayant constaté cette panne ! ")
	 *
	 */
	private $constatateur;
	
	/**
	 * 
	 * @var \DateTime
	 * 
	 *  @ORM\Column(name="date_constat", type="date", nullable=true)
	 *  @Assert\NotBlank(message="Choisissez la date de constat ")
	 */
	private $dateConstat;
	
	/**
	 * 
	 * @var \DateTime
	 * 
	 *  @ORM\Column(name="date_signale", type="date", nullable=true)
	 */
	private $dateSignale;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="etat_courant", type="string", length=255, nullable=true)
	 * @Assert\NotBlank()
	 *
	 */
	private $etatCourant;
	
	/**
	 * @ORM\ManyToOne(targetEntity="TypeAction", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_signalisation_id", referencedColumnName="id")
	 * })
	 * @Assert\NotBlank(message="Donnez le type de l'action ")
	 *
	 */
	private $typeSignalisation;
	
	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Domaine", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
	 * })
	 *
	 * @Assert\NotBlank(message="Donnez le domaine de l'action ")
	 *
	 */
	private $domaine;
	
	public $toDateConstat;
	
	public $fromDateConstat;
	
	public $toDateSignale;
	
	public $fromDateSignale;
	
	/**
	 * @ORM\OneToMany(targetEntity="SignalisationReformulation", mappedBy="signalisation", cascade={"persist","remove","merge"})
	 */
	private $reformulation;
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->signStatut = new \Doctrine\Common\Collections\ArrayCollection();
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dateSignale=new \DateTime();
        $this->reference = "SIGNALISATION_".$this->getId().strtoupper(ActionUtils::random(10));
        $this->etatCourant = Statut::NOUVELLE_SIGNALISATION;
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
     * Set libelle
     *
     * @param string $libelle
     * @return Signalisation
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Signalisation
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Signalisation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add signStatut
     *
     * @param \Orange\MainBundle\Entity\SignalisationStatut $signStatut
     * @return Signalisation
     */
    public function addSignStatut(\Orange\MainBundle\Entity\SignalisationStatut $signStatut)
    {
        $this->signStatut[] = $signStatut;

        return $this;
    }

    /**
     * Remove signStatut
     *
     * @param \Orange\MainBundle\Entity\SignalisationStatut $signStatut
     */
    public function removeSignStatut(\Orange\MainBundle\Entity\SignalisationStatut $signStatut)
    {
        $this->signStatut->removeElement($signStatut);
    }

    /**
     * Set source
     *
     * @param \Orange\MainBundle\Entity\Source $source
     * @return Signalisation
     */
    public function setSource(\Orange\MainBundle\Entity\Source $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Orange\MainBundle\Entity\Source 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set dateConstat
     *
     * @param \DateTime $dateConstat
     * @return Signalisation
     */
    public function setDateConstat($dateConstat)
    {
        $this->dateConstat = $dateConstat;

        return $this;
    }

    /**
     * Get dateConstat
     *
     * @return \DateTime 
     */
    public function getDateConstat()
    {
        return $this->dateConstat;
    }

    /**
     * Set dateSignale
     *
     * @param \DateTime $dateSignale
     * @return Signalisation
     */
    public function setDateSignale($dateSignale)
    {
        $this->dateSignale = $dateSignale;

        return $this;
    }

    /**
     * Get dateSignale
     *
     * @return \DateTime 
     */
    public function getDateSignale()
    {
        return $this->dateSignale;
    }
    
    public function __toString()
    {
    	return $this->libelle;
    }

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Signalisation
     */
    public function addAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action[] = $action;

        return $this;
    }

    /**
     * Remove action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     */
    public function removeAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action->removeElement($action);
    }

    /**
     * Get action
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get signStatut
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignStatut()
    {
        return $this->signStatut;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Signalisation
     */
    public function setInstance(\Orange\MainBundle\Entity\Instance $instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return \Orange\MainBundle\Entity\Instance 
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Set constatateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $constatateur
     * @return Signalisation
     */
    public function setConstatateur(\Orange\MainBundle\Entity\Utilisateur $constatateur = null)
    {
        $this->constatateur = $constatateur;

        return $this;
    }

    /**
     * Get constatateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getConstatateur()
    {
        return $this->constatateur;
    }

    /**
     * Add signalisationAnimateur
     *
     * @param \Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur
     * @return Signalisation
     */
    public function addSignalisationAnimateur(\Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur)
    {
        $this->signalisationAnimateur[] = $signalisationAnimateur;

        return $this;
    }

    /**
     * Remove signalisationAnimateur
     *
     * @param \Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur
     */
    public function removeSignalisationAnimateur(\Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur)
    {
        $this->signalisationAnimateur->removeElement($signalisationAnimateur);
    }

    /**
     * Get signalisationAnimateur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignalisationAnimateur()
    {
        return $this->signalisationAnimateur;
    }

    /**
     * Set etatCourant
     *
     * @param string $etatCourant
     * @return Signalisation
     */
    public function setEtatCourant($etatCourant)
    {
        $this->etatCourant = $etatCourant;

        return $this;
    }

    /**
     * Get etatCourant
     *
     * @return string 
     */
    public function getEtatCourant()
    {
        return $this->etatCourant;
    }
    
    /**
     * Set site
     *
     * @param string $site
     *
     * @return Signalisation
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }
	public function getTypeSignalisation(){
		return $this->typeSignalisation;
	}
	public function setTypeSignalisation($typeSignalisation) {
		$this->typeSignalisation = $typeSignalisation;
		return $this;
	}
	public function getDomaine() {
		return $this->domaine;
	}
	public function setDomaine($domaine) {
		$this->domaine = $domaine;
		return $this;
	}
	public function getToDateConstat() {
		return $this->toDateConstat;
	}
	public function setToDateConstat($toDateConstat) {
		$this->toDateConstat = $toDateConstat;
		return $this;
	}
	public function getFromDateConstat() {
		return $this->fromDateConstat;
	}
	public function setFromDateConstat($fromDateConstat) {
		$this->fromDateConstat = $fromDateConstat;
		return $this;
	}
	public function getToDateSignale() {
		return $this->toDateSignale;
	}
	public function setToDateSignale($toDateSignale) {
		$this->toDateSignale = $toDateSignale;
		return $this;
	}
	public function getFromDateSignale() {
		return $this->fromDateSignale;
	}
	public function setFromDateSignale($fromDateSignale) {
		$this->fromDateSignale = $fromDateSignale;
		return $this;
	}
	public function getPerimetre() {
		return $this->perimetre;
	}
	public function setPerimetre($perimetre) {
		$this->perimetre = $perimetre;
		return $this;
	}
	public function getConstat() {
		return $this->constat;
	}
	public function setConstat($constat) {
		$this->constat = $constat;
		return $this;
	}
	public function getStatut() {
		return $this->statut;
	}
	public function setStatut($statut) {
		$this->statut = $statut;
		return $this;
	}
	public function getDom() {
		return $this->dom;
	}
	public function setDom($dom) {
		$this->dom = $dom;
		return $this;
	}
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
		return $this;
	}
	public function getUtilisateur() {
		return $this->utilisateur;
	}
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	
    

    /**
     * Add reformulation
     *
     * @param \Orange\MainBundle\Entity\SignalisationReformulation $reformulation
     *
     * @return Signalisation
     */
    public function addReformulation(\Orange\MainBundle\Entity\SignalisationReformulation $reformulation)
    {
        $this->reformulation[] = $reformulation;

        return $this;
    }

    /**
     * Remove reformulation
     *
     * @param \Orange\MainBundle\Entity\SignalisationReformulation $reformulation
     */
    public function removeReformulation(\Orange\MainBundle\Entity\SignalisationReformulation $reformulation)
    {
        $this->reformulation->removeElement($reformulation);
    }

    /**
     * Get reformulation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReformulation()
    {
        return $this->reformulation;
    }
}

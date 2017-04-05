<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SignalisationReformulation
 *
 * @ORM\Table(name="signalisation_reformulation")
 * @ORM\Entity()
 */
class SignalisationReformulation
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
     * 
     */
	private $libelle;
        
       /**
	 * @var string
	 *
	 * @ORM\Column(name="site", type="string", length=45, nullable=true)
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
     * 
	 */
	private $description;
	
	
	/**
	 * @var \Animateur
	 * @ORM\ManyToOne(targetEntity="Instance")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id", nullable=true)
	 * })
	 */
	private $instance;
	
	/**
	 * @var \Source
	 *
	 * @ORM\ManyToOne(targetEntity="Source")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=true)
	 * })
	 *
	 */
	private $source;
	
	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="constatateur", referencedColumnName="id", nullable=true)
	 * })
	 *
	 */
	private $constatateur;
	
	/**
	 * 
	 * @var \DateTime
	 * 
	 *  @ORM\Column(name="date_constat", type="date", nullable=true)
	 */
	private $dateConstat;
	
	/**
	 * 
	 * @var \DateTime
	 * 
	 *  @ORM\Column(name="date", type="date", nullable=true)
	 */
	private $date;
	
	
	/**
	 * @ORM\ManyToOne(targetEntity="TypeAction", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_signalisation_id", referencedColumnName="id", nullable=true)
	 * })
	 *
	 */
	private $typeSignalisation;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="signalisation_id", referencedColumnName="id", nullable=true)
	 * })
	 *
	 */
	private $signalisation;
	
	/**
	 *
	 * @ORM\ManyToOne(targetEntity="Domaine", inversedBy="signalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id", nullable=true)
	 * })
	 *
	 */
	private $domaine;
	
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->date = new \DateTime();
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
     *
     * @return SignalisationReformulation
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
     * Set site
     *
     * @param string $site
     *
     * @return SignalisationReformulation
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

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return SignalisationReformulation
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
     *
     * @return SignalisationReformulation
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
     * Set dateConstat
     *
     * @param \DateTime $dateConstat
     *
     * @return SignalisationReformulation
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return SignalisationReformulation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     *
     * @return SignalisationReformulation
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
     * Set source
     *
     * @param \Orange\MainBundle\Entity\Source $source
     *
     * @return SignalisationReformulation
     */
    public function setSource(\Orange\MainBundle\Entity\Source $source = null)
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
     * Set constatateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $constatateur
     *
     * @return SignalisationReformulation
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
     * Set typeSignalisation
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeSignalisation
     *
     * @return SignalisationReformulation
     */
    public function setTypeSignalisation(\Orange\MainBundle\Entity\TypeAction $typeSignalisation = null)
    {
        $this->typeSignalisation = $typeSignalisation;

        return $this;
    }

    /**
     * Get typeSignalisation
     *
     * @return \Orange\MainBundle\Entity\TypeAction
     */
    public function getTypeSignalisation()
    {
        return $this->typeSignalisation;
    }

    /**
     * Set signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     *
     * @return SignalisationReformulation
     */
    public function setSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation = null)
    {
        $this->signalisation = $signalisation;

        return $this;
    }

    /**
     * Get signalisation
     *
     * @return \Orange\MainBundle\Entity\Signalisation
     */
    public function getSignalisation()
    {
        return $this->signalisation;
    }

    /**
     * Set domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     *
     * @return SignalisationReformulation
     */
    public function setDomaine(\Orange\MainBundle\Entity\Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return \Orange\MainBundle\Entity\Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }
}

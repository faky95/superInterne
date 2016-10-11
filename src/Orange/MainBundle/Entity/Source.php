<?php

namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Source
 *
 * @ORM\Table(name="instance_has_source")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\SourceRepository")
 */
class Source
{

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	 
	/**
	 * @var \Utilisateur
	 *
	 * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="sources")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;

	/**
	 * @ORM\ManyToOne(targetEntity="Instance", inversedBy="sourceInstance")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $instance;
	
	/**
	 * @ORM\OneToMany(targetEntity="Signalisation", mappedBy="source", cascade={"persist","remove","merge"})
	 */
	private $signalisation;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_affectation", type="date", nullable=true)
	 */
	private $dateAffectation;


	
	public function __toString(){
		return $this->utilisateur->__toString();
	}
	
	public function __construct(){
		return $this->getUtilisateur()." ";
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
     * Set dateAffectation
     *
     * @param \DateTime $dateAffectation
     * @return Source
     */
    public function setDateAffectation($dateAffectation)
    {
        $this->dateAffectation = $dateAffectation;

        return $this;
    }

    /**
     * Get dateAffectation
     *
     * @return \DateTime 
     */
    public function getDateAffectation()
    {
        return $this->dateAffectation;
    }

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Source
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Source
     */
    public function setInstance(\Orange\MainBundle\Entity\Instance $instance)
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
}

<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Animateur Membre
 *
 * @ORM\Table(name="animateur_membre")
 * @ORM\Entity
 */
class AnimateurMembre
{



/**
 * @var integer
 *
 * @ORM\Column(name="id", type="integer", nullable=false)
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="IDENTITY")
 */
private  $id;
 
/**
 * @var \Utilisateur
 *
 * @ORM\ManyToOne(targetEntity="Utilisateur")
 * @ORM\JoinColumns({
 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
 * })
 */
private $utilisateur;

/**
 * @var \Instance
 *
 * @ORM\ManyToOne(targetEntity="Instance", inversedBy="animateur")
 * @ORM\JoinColumn(nullable=false)
 */
private $instance;


/**
 * @var \DateTime
 *
 * @ORM\Column(name="date_affectation", type="date", nullable=true)
 */
private $dateAffectation;


		public function __construct(){
				$this->setDateAffectation(new  \DateTime('now'));
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
     * @return Animateur
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
     * @return Animateur
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
     * @return Animateur
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

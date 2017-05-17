<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 * @ORM\Table(name="signalisation_has_animateur")
 * @ORM\Entity()
 */
class SignalisationAnimateur
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
     * @ORM\ManyToOne(targetEntity="Signalisation", inversedBy="signalisationAnimateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $signalisation;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStatut", type="date", nullable=true)
     */
    private $dateAffectation;
   
    /**
     * @var \Boolean
     *
     * @ORM\Column(name="en_cours", type="boolean", nullable=true)
     */
    private $actif;
    
    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="signalisationAnimateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
    
    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;
    
    public function __construct(){
    	$this->dateAffectation = new \DateTime();
    	$this->actif = false;
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
     * @return SignalisationAnimateur
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
     * Set actif
     *
     * @param boolean $actif
     * @return SignalisationAnimateur
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean 
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     * @return SignalisationAnimateur
     */
    public function setSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
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
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return SignalisationAnimateur
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
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
     * Set commentaire
     *
     * @param string $commentaire
     * @return SignalisationAnimateur
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string 
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }
}

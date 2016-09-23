<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membre
 *
 * @ORM\Table(name="membre_projet")
 * @ORM\Entity
 */
class MembreProjet
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
      * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="membreProjet")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
      * })
      */
      private $utilisateur;
      
     /**
      * @var \Projet
      *
      * @ORM\ManyToOne(targetEntity="Projet", inversedBy="membreProjet")
      * @ORM\JoinColumn(nullable=false)
      */
      private $projet;
      
      
      /**
       * @var \DateTime
       *
       * @ORM\Column(name="date_affectation", type="date", nullable=true)
       */
      private $dateAffectation;
      
      
      public function __construct(){
      	$this->setDateAffectation(new  \DateTime('now'));
      	$this->isChef=0;
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
     * @return MembreProjet
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
     * @return MembreProjet
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
     * Set projet
     *
     * @param \Orange\MainBundle\Entity\Projet $projet
     * @return MembreProjet
     */
    public function setProjet(\Orange\MainBundle\Entity\Projet $projet)
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * Get projet
     *
     * @return \Orange\MainBundle\Entity\Projet 
     */
    public function getProjet()
    {
        return $this->projet;
    }
}

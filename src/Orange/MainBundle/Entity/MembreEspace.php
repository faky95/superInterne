<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membre d'un espace
 *
 * @ORM\Table(name="membre_espace")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\MembreEspaceRepository")
 */
class MembreEspace
{
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
     private  $id;
     
     /**
      * @var \Utilisateur
      *
      * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="membreEspace")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
      * })
      */
      private $utilisateur;
      
     /**
      * @ORM\ManyToOne(targetEntity="Espace", inversedBy="membreEspace")
      * @ORM\JoinColumn(nullable=false)
      */
      private $espace;
      
      
      /**
       * @var \DateTime
       *
       * @ORM\Column(name="date_affectation", type="date", nullable=true)
       */
      private $dateAffectation;
      

      /**
       * @var boolean
       *
       * @ORM\Column(name="is_gestionnaire", type="boolean", nullable=true)
       */
      private $isGestionnaire;
      
      
      
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
     * @return Membre
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
     * @return Membre
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
     * Set espace
     *
     * @param \Orange\MainBundle\Entity\Espace $espace
     * @return Membre
     */
    public function setEspace(\Orange\MainBundle\Entity\Espace $espace)
    {
        $this->espace = $espace;

        return $this;
    }

    /**
     * Get espace
     *
     * @return \Orange\MainBundle\Entity\Espace 
     */
    public function getEspace()
    {
        return $this->espace;
    }
    

	

    /**
     * Set isGestionnaire
     *
     * @param boolean $isGestionnaire
     * @return MembreEspace
     */
    public function setIsGestionnaire($isGestionnaire)
    {
        $this->isGestionnaire = $isGestionnaire;

        return $this;
    }

    /**
     * Get isGestionnaire
     *
     * @return boolean 
     */
    public function getIsGestionnaire()
    {
        return $this->isGestionnaire;
    }
}

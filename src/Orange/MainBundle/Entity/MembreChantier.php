<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Membre
 *
 * @ORM\Table(name="membre_chantier")
 * @ORM\Entity
 */
class MembreChantier
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
      * @var \Chantier
      *
      * @ORM\ManyToOne(targetEntity="Chantier", inversedBy="membreChantier")
      * @ORM\JoinColumn(nullable=false)
      */
      private $chantier;
      
      
      /**
       * @var \DateTime
       *
       * @ORM\Column(name="date_affectation", type="date", nullable=true)
       */
      private $dateAffectation;
      
      

      /**
       * @var integer
       *
       * @ORM\Column(name="is_chef", type="integer", nullable=true)
       */
      private $isChef;
      
      
      
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
     * @return MembreChantier
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
     * Set isChef
     *
     * @param integer $isChef
     * @return MembreChantier
     */
    public function setIsChef($isChef)
    {
        $this->isChef = $isChef;

        return $this;
    }

    /**
     * Get isChef
     *
     * @return integer 
     */
    public function getIsChef()
    {
        return $this->isChef;
    }

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return MembreChantier
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
     * Set chantier
     *
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     * @return MembreChantier
     */
    public function setChantier(\Orange\MainBundle\Entity\Chantier $chantier)
    {
        $this->chantier = $chantier;

        return $this;
    }

    /**
     * Get chantier
     *
     * @return \Orange\MainBundle\Entity\Chantier 
     */
    public function getChantier()
    {
        return $this->chantier;
    }
}

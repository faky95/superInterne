<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParametrageBu
 *
 * @ORM\Table(name="parametrage_bu")
 * @ORM\Entity
 */
class ParametrageBu
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
	 * @var boolean
	 * @ORM\Column(name="has_validation", type="boolean", nullable=true)
	 */
	private  $hasValidation;
	
	/**
	 * @var boolean
	 * @ORM\Column(name="has_signalisation", type="boolean", nullable=true)
	 */
	private  $hasSignalisation;
	
	/**
	 * @var Integer
	 * @ORM\Column(name="affichage_stats", type="integer", nullable=true)
	 */
	 private  $affichageStats;
	
	 /**
	 * 
	 * @var Bu
	 * @ORM\OneToOne(targetEntity="Bu", inversedBy="parametrage", cascade={"persist","remove","merge"})
	 */
	private $bu;
	 
	 /**
	  * @var String
	  * @ORM\Column(name="couleur", type="string", nullable=true)
	  */
	 private  $couleur;
	 
	 /**
	  * @var String
	  * @ORM\Column(name="logo_image", type="string", nullable=true)
	  */
	 private  $logoImage;
	 
	 /**
	  * @var String
	  * @ORM\Column(name="logo_texte", type="string", nullable=true)
	  */
	 private  $logoTexte;
	 
	 /**
	  * @var Boolean
	  * @ORM\Column(name="is_image", type="boolean", nullable=true)
	  */
	 private  $isImage;
	 
	 /**
	  * @var \Entete
	  *
	  * @ORM\ManyToOne(targetEntity="Entete")
	  * @ORM\JoinColumns({
	  *   @ORM\JoinColumn(name="entete_id", referencedColumnName="id")
	  * })
	  */
	 private $entete;
	 
	 
	 

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
     * Set hasValidation
     *
     * @param boolean $hasValidation
     *
     * @return ParametrageBu
     */
    public function setHasValidation($hasValidation)
    {
        $this->hasValidation = $hasValidation;

        return $this;
    }

    /**
     * Get hasValidation
     *
     * @return boolean
     */
    public function getHasValidation()
    {
        return $this->hasValidation;
    }

    /**
     * Set hasSignalisation
     *
     * @param boolean $hasSignalisation
     *
     * @return ParametrageBu
     */
    public function setHasSignalisation($hasSignalisation)
    {
        $this->hasSignalisation = $hasSignalisation;

        return $this;
    }

    /**
     * Get hasSignalisation
     *
     * @return boolean
     */
    public function getHasSignalisation()
    {
        return $this->hasSignalisation;
    }

    /**
     * Set affichageStats
     *
     * @param integer $affichageStats
     *
     * @return ParametrageBu
     */
    public function setAffichageStats($affichageStats)
    {
        $this->affichageStats = $affichageStats;

        return $this;
    }

    /**
     * Get affichageStats
     *
     * @return integer
     */
    public function getAffichageStats()
    {
        return $this->affichageStats;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     *
     * @return ParametrageBu
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set logoImage
     *
     * @param string $logoImage
     *
     * @return ParametrageBu
     */
    public function setLogoImage($logoImage)
    {
        $this->logoImage = $logoImage;

        return $this;
    }

    /**
     * Get logoImage
     *
     * @return string
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * Set logoTexte
     *
     * @param string $logoTexte
     *
     * @return ParametrageBu
     */
    public function setLogoTexte($logoTexte)
    {
        $this->logoTexte = $logoTexte;

        return $this;
    }

    /**
     * Get logoTexte
     *
     * @return string
     */
    public function getLogoTexte()
    {
        return $this->logoTexte;
    }

    /**
     * Set isImage
     *
     * @param boolean $isImage
     *
     * @return ParametrageBu
     */
    public function setIsImage($isImage)
    {
        $this->isImage = $isImage;

        return $this;
    }

    /**
     * Get isImage
     *
     * @return boolean
     */
    public function getIsImage()
    {
        return $this->isImage;
    }

   
    /**
     * Set entete
     *
     * @param \Orange\MainBundle\Entity\Entete $entete
     *
     * @return ParametrageBu
     */
    public function setEntete(\Orange\MainBundle\Entity\Entete $entete = null)
    {
        $this->entete = $entete;

        return $this;
    }

    /**
     * Get entete
     *
     * @return \Orange\MainBundle\Entity\Entete
     */
    public function getEntete()
    {
        return $this->entete;
    }

    /**
     * Set bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     *
     * @return ParametrageBu
     */
    public function setBu(\Orange\MainBundle\Entity\Bu $bu = null)
    {
        $this->bu = $bu;

        return $this;
    }

    /**
     * Get bu
     *
     * @return \Orange\MainBundle\Entity\Bu
     */
    public function getBu()
    {
        return $this->bu;
    }
}

<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistique
 * @ORM\Table(name="statistiqueSign")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\StatistiqueSignRepository")
 */
class StatistiqueSign
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
	 * @var integer
	 *
	 * @ORM\Column(name="nb_efficace", type="integer", length=50, nullable=true)
	 */
	private  $nbEfficace;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_non_efficace", type="integer", length=50, nullable=true)
	 */
	private $nbNonEfficace;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_en_cours", type="integer", length=50, nullable=true)
	 */
	private $nbEnCours;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_cloturee", type="integer", length=50, nullable=true)
	 */
	private $nbCloturee;
	
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="semaine", type="integer", length=50, nullable=true)
	 */
	private $semaine;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="annee", type="integer", length=50, nullable=true)
	 */
	private $annee;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_total", type="integer", length=50, nullable=true)
	 */
	private $nbTotal;
	/**
	 * @var \Instance
	 *
	 * @ORM\ManyToOne(targetEntity="Instance", inversedBy="statistiqueSignalisation")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $instance;
	
	/**
	 * @var \Utilisateur
	 *
	 * @ORM\ManyToOne(targetEntity="Source", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="source_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $source;
	
	/**
	 * @var \Strucutre
	 *
	 * @ORM\ManyToOne(targetEntity="Structure", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $structure;
	


	
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
     * Set nbEfficace
     *
     * @param integer $nbEfficace
     *
     * @return StatistiqueSign
     */
    public function setNbEfficace($nbEfficace)
    {
        $this->nbEfficace = $nbEfficace;

        return $this;
    }

    /**
     * Get nbEfficace
     *
     * @return integer
     */
    public function getNbEfficace()
    {
        return $this->nbEfficace;
    }

    /**
     * Set nbNonEfficace
     *
     * @param integer $nbNonEfficace
     *
     * @return StatistiqueSign
     */
    public function setNbNonEfficace($nbNonEfficace)
    {
        $this->nbNonEfficace = $nbNonEfficace;

        return $this;
    }

    /**
     * Get nbNonEfficace
     *
     * @return integer
     */
    public function getNbNonEfficace()
    {
        return $this->nbNonEfficace;
    }

    /**
     * Set nbEnCours
     *
     * @param integer $nbEnCours
     *
     * @return StatistiqueSign
     */
    public function setNbEnCours($nbEnCours)
    {
        $this->nbEnCours = $nbEnCours;

        return $this;
    }

    /**
     * Get nbEnCours
     *
     * @return integer
     */
    public function getNbEnCours()
    {
        return $this->nbEnCours;
    }

    /**
     * Set nbCloturee
     *
     * @param integer $nbCloturee
     *
     * @return StatistiqueSign
     */
    public function setNbCloturee($nbCloturee)
    {
        $this->nbCloturee = $nbCloturee;

        return $this;
    }

    /**
     * Get nbCloturee
     *
     * @return integer
     */
    public function getNbCloturee()
    {
        return $this->nbCloturee;
    }

    /**
     * Set semaine
     *
     * @param integer $semaine
     *
     * @return StatistiqueSign
     */
    public function setSemaine($semaine)
    {
        $this->semaine = $semaine;

        return $this;
    }

    /**
     * Get semaine
     *
     * @return integer
     */
    public function getSemaine()
    {
        return $this->semaine;
    }

    /**
     * Set annee
     *
     * @param integer $annee
     *
     * @return StatistiqueSign
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return integer
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     *
     * @return StatistiqueSign
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
     * @return StatistiqueSign
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
     * Set nbTotal
     *
     * @param integer $nbTotal
     *
     * @return StatistiqueSign
     */
    public function setNbTotal($nbTotal)
    {
        $this->nbTotal = $nbTotal;

        return $this;
    }

    /**
     * Get nbTotal
     *
     * @return integer
     */
    public function getNbTotal()
    {
        return $this->nbTotal;
    }

    /**
     * Set structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     *
     * @return StatistiqueSign
     */
    public function setStructure(\Orange\MainBundle\Entity\Structure $structure = null)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get structure
     *
     * @return \Orange\MainBundle\Entity\Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }
}

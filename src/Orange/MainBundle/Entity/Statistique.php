<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistique
 * @ORM\Table(name="statistique")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\StatistiqueRepository")
 */
class Statistique
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
	 * @ORM\Column(name="abandonnee", type="integer", length=50, nullable=true)
	 */
	private  $nbAbandon;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="demande_abandon", type="integer", length=50, nullable=true)
	 */
	private $nbDemandeAbandon;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_demande_report", type="integer", length=50, nullable=true)
	 */
	private $nbDemandeReport;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="echue_non_soldee", type="integer", length=50, nullable=true)
	 */
	private $nbEchueNonSoldee;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="non_echue_non_soldee", type="integer", length=50, nullable=true)
	 */
	private $nbNonEchueNonSoldee;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_faite", type="integer", length=50, nullable=true)
	 */
	private $nbFaite;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_fait_delai", type="integer", length=50, nullable=true)
	 */
	private $nbFaiteDelai;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_fait_hors_delai", type="integer", length=50, nullable=true)
	 */
	private $nbFaiteHorsDelai;
	
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="non_echue", type="integer", length=50, nullable=true)
	 */
	private $nbNonEchue;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_en_cours", type="integer", length=50, nullable=true)
	 */
	private $nbEnCours;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_soldee", type="integer", length=50, nullable=true)
	 */
	private $nbSoldee;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_solde_delai", type="integer", length=50, nullable=true)
	 */
	private $nbSoldeeDansLesDelais;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_solde_hors_delai", type="integer", length=50, nullable=true)
	 */
	private $nbSoldeeHorsDelais;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_utilisateur_actif", type="integer", length=50, nullable=true)
	 */
	private $nbUtilisateurActif;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="nb_utilisateur", type="integer", length=50, nullable=true)
	 */
	private $nbUtilisateur;
	
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="total", type="integer", length=50, nullable=true)
	 */
	private $total;
	
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
	 * @var \Instance
	 *
	 * @ORM\ManyToOne(targetEntity="Instance", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $instance;
	
	
	/**
	 * @var \Structure
	 *
	 * @ORM\ManyToOne(targetEntity="Structure", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $structure;
	
	/**
	 * @var \Utilisateur
	 *
	 * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $utilisateur;
	
	
	/**
	 * @var \TypeActeur
	 *
	 * @ORM\ManyToOne(targetEntity="TypeActeur", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $type;
	
	/**
	 * @var \Domaine
	 *
	 * @ORM\ManyToOne(targetEntity="Domaine",inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $domaine;
	
	/**
	 * @var \TypeAction
	 *
	 * @ORM\ManyToOne(targetEntity="TypeAction", inversedBy="statistique")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
	 * })
	 *
	 */
	private $typeAction;
	
	public $instances;
	
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
     * Set nbAbandon
     *
     * @param integer $nbAbandon
     *
     * @return Statistique
     */
    public function setNbAbandon($nbAbandon)
    {
        $this->nbAbandon = $nbAbandon;

        return $this;
    }

    /**
     * Get nbAbandon
     *
     * @return integer
     */
    public function getNbAbandon()
    {
        return $this->nbAbandon;
    }

    /**
     * Set nbDemandeAbandon
     *
     * @param integer $nbDemandeAbandon
     *
     * @return Statistique
     */
    public function setNbDemandeAbandon($nbDemandeAbandon)
    {
        $this->nbDemandeAbandon = $nbDemandeAbandon;

        return $this;
    }

    /**
     * Get nbDemandeAbandon
     *
     * @return integer
     */
    public function getNbDemandeAbandon()
    {
        return $this->nbDemandeAbandon;
    }

    /**
     * Set nbEchueNonSoldee
     *
     * @param integer $nbEchueNonSoldee
     *
     * @return Statistique
     */
    public function setNbEchueNonSoldee($nbEchueNonSoldee)
    {
        $this->nbEchueNonSoldee = $nbEchueNonSoldee;

        return $this;
    }

    /**
     * Get nbEchueNonSoldee
     *
     * @return integer
     */
    public function getNbEchueNonSoldee()
    {
        return $this->nbEchueNonSoldee;
    }

    /**
     * Set nbFaite
     *
     * @param integer $nbFaite
     *
     * @return Statistique
     */
    public function setNbFaite($nbFaite)
    {
        $this->nbFaite = $nbFaite;

        return $this;
    }

    /**
     * Get nbFaite
     *
     * @return integer
     */
    public function getNbFaite()
    {
        return $this->nbFaite;
    }

    /**
     * Set nbNonEchue
     *
     * @param integer $nbNonEchue
     *
     * @return Statistique
     */
    public function setNbNonEchue($nbNonEchue)
    {
        $this->nbNonEchue = $nbNonEchue;

        return $this;
    }

    /**
     * Get nbNonEchue
     *
     * @return integer
     */
    public function getNbNonEchue()
    {
        return $this->nbNonEchue;
    }

    /**
     * Set nbSoldee
     *
     * @param integer $nbSoldee
     *
     * @return Statistique
     */
    public function setNbSoldee($nbSoldee)
    {
        $this->nbSoldee = $nbSoldee;

        return $this;
    }

    /**
     * Get nbSoldee
     *
     * @return integer
     */
    public function getNbSoldee()
    {
        return $this->nbSoldee;
    }

    /**
     * Set nbEnCours
     *
     * @param integer $nbEnCours
     *
     * @return Statistique
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
     * Set nbSoldeeDansLesDelais
     *
     * @param integer $nbSoldeeDansLesDelais
     *
     * @return Statistique
     */
    public function setNbSoldeeDansLesDelais($nbSoldeeDansLesDelais)
    {
        $this->nbSoldeeDansLesDelais = $nbSoldeeDansLesDelais;

        return $this;
    }

    /**
     * Get nbSoldeeDansLesDelais
     *
     * @return integer
     */
    public function getNbSoldeeDansLesDelais()
    {
        return $this->nbSoldeeDansLesDelais;
    }

    /**
     * Set total
     *
     * @param integer $total
     *
     * @return Statistique
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set semaine
     *
     * @param integer $semaine
     *
     * @return Statistique
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
     * @return Statistique
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
     * Set nbUtilisateurActif
     *
     * @param integer $nbUtilisateurActif
     *
     * @return Statistique
     */
    public function setNbUtilisateurActif($nbUtilisateurActif)
    {
        $this->nbUtilisateurActif = $nbUtilisateurActif;

        return $this;
    }

    /**
     * Get nbUtilisateurActif
     *
     * @return integer
     */
    public function getNbUtilisateurActif()
    {
        return $this->nbUtilisateurActif;
    }

    /**
     * Set nbUtilisateur
     *
     * @param integer $nbUtilisateur
     *
     * @return Statistique
     */
    public function setNbUtilisateur($nbUtilisateur)
    {
        $this->nbUtilisateur = $nbUtilisateur;

        return $this;
    }

    /**
     * Get nbUtilisateur
     *
     * @return integer
     */
    public function getNbUtilisateur()
    {
        return $this->nbUtilisateur;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     *
     * @return Statistique
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
     * Set structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     *
     * @return Statistique
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

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     *
     * @return Statistique
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
     * Set type
     *
     * @param \Orange\MainBundle\Entity\TypeActeur $type
     *
     * @return Statistique
     */
    public function setType(\Orange\MainBundle\Entity\TypeActeur $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Orange\MainBundle\Entity\TypeActeur
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nbNonEchueNonSoldee
     *
     * @param integer $nbNonEchueNonSoldee
     *
     * @return Statistique
     */
    public function setNbNonEchueNonSoldee($nbNonEchueNonSoldee)
    {
        $this->nbNonEchueNonSoldee = $nbNonEchueNonSoldee;

        return $this;
    }

    /**
     * Get nbNonEchueNonSoldee
     *
     * @return integer
     */
    public function getNbNonEchueNonSoldee()
    {
        return $this->nbNonEchueNonSoldee;
    }

    /**
     * Set nbFaiteDelai
     *
     * @param integer $nbFaiteDelai
     *
     * @return Statistique
     */
    public function setNbFaiteDelai($nbFaiteDelai)
    {
        $this->nbFaiteDelai = $nbFaiteDelai;

        return $this;
    }

    /**
     * Get nbFaiteDelai
     *
     * @return integer
     */
    public function getNbFaiteDelai()
    {
        return $this->nbFaiteDelai;
    }

    /**
     * Set nbFaiteHorsDelai
     *
     * @param integer $nbFaiteHorsDelai
     *
     * @return Statistique
     */
    public function setNbFaiteHorsDelai($nbFaiteHorsDelai)
    {
        $this->nbFaiteHorsDelai = $nbFaiteHorsDelai;

        return $this;
    }

    /**
     * Get nbFaiteHorsDelai
     *
     * @return integer
     */
    public function getNbFaiteHorsDelai()
    {
        return $this->nbFaiteHorsDelai;
    }

    /**
     * Set nbSoldeeHorsDelais
     *
     * @param integer $nbSoldeeHorsDelais
     *
     * @return Statistique
     */
    public function setNbSoldeeHorsDelais($nbSoldeeHorsDelais)
    {
        $this->nbSoldeeHorsDelais = $nbSoldeeHorsDelais;

        return $this;
    }

    /**
     * Get nbSoldeeHorsDelais
     *
     * @return integer
     */
    public function getNbSoldeeHorsDelais()
    {
        return $this->nbSoldeeHorsDelais;
    }

    /**
     * Set domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     *
     * @return Statistique
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

    /**
     * Set typeAction
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     *
     * @return Statistique
     */
    public function setTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction = null)
    {
        $this->typeAction = $typeAction;

        return $this;
    }

    /**
     * Get typeAction
     *
     * @return \Orange\MainBundle\Entity\TypeAction
     */
    public function getTypeAction()
    {
        return $this->typeAction;
    }

    /**
     * Set nbDemandeReport
     *
     * @param integer $nbDemandeReport
     *
     * @return Statistique
     */
    public function setNbDemandeReport($nbDemandeReport)
    {
        $this->nbDemandeReport = $nbDemandeReport;

        return $this;
    }

    /**
     * Get nbDemandeReport
     *
     * @return integer
     */
    public function getNbDemandeReport()
    {
        return $this->nbDemandeReport;
    }
}

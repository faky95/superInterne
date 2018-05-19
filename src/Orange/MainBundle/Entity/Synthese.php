<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Synthese
 * @ORM\Table(name="synthese", uniqueConstraints={
 *        @UniqueConstraint(columns={"instance_id", "structure_id", "utilisateur_id", "domaine_id", "type_action_id", "type_id"})
 *    })
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\SyntheseRepository")
 */
class Synthese
{
	/**
	 * @var array
	 */
	static $fields = array(
			'nbAbandon', 'nbDemandeAbandon', 'nbDemandeReport', 'nbEchueNonSoldee', 'nbNonEchue', 'nbFaiteDelai', 
			'nbFaiteHorsDelai','nbSoldeeDansLesDelais', 'nbSoldeeHorsDelais', 'total'
		);
	
	/**
	 * @var array
	 */
	static $formules = array('respectDelai' => 'Taux de respect du délai');
	
	/**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private  $id;
	
	/**
	 * @var integer
	 * @ORM\Column(name="abandonnee", type="integer", length=50, nullable=true)
	 */
	private  $nbAbandon;
	
	/**
	 * @var integer
	 * @ORM\Column(name="demande_abandon", type="integer", length=50, nullable=true)
	 */
	private $nbDemandeAbandon;
	
	/**
	 * @var integer
	 * @ORM\Column(name="nb_demande_report", type="integer", length=50, nullable=true)
	 */
	private $nbDemandeReport;
	
	/**
	 * @var integer
	 * @ORM\Column(name="echue_non_soldee", type="integer", length=50, nullable=true)
	 */
	private $nbEchueNonSoldee;
	
	/**
	 * @var integer
	 * @ORM\Column(name="non_echue", type="integer", length=50, nullable=true)
	 */
	private $nbNonEchue;
	
	/**
	 * @var integer
	 * @ORM\Column(name="nb_fait_delai", type="integer", length=50, nullable=true)
	 */
	private $nbFaiteDelai;
	
	/**
	 * @var integer
	 * @ORM\Column(name="nb_fait_hors_delai", type="integer", length=50, nullable=true)
	 */
	private $nbFaiteHorsDelai;
	
	/**
	 * @var integer
	 * @ORM\Column(name="nb_solde_delai", type="integer", length=50, nullable=true)
	 */
	private $nbSoldeeDansLesDelais;
	
	/**
	 * @var integer
	 * @ORM\Column(name="nb_solde_hors_delai", type="integer", length=50, nullable=true)
	 */
	private $nbSoldeeHorsDelais;
	
	/**
	 * @var integer
	 * @ORM\Column(name="total", type="integer", length=50, nullable=true)
	 */
	private $total;
	
	/**
	 * @var float
	 * @ORM\Column(name="respect_delai", type="float", nullable=true)
	 */
	private $respectDelai;
	
	/**
	 * @var Instance
	 * @ORM\ManyToOne(targetEntity="Instance")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 * })
	 */
	private $instance;
	
	
	/**
	 * @var Structure
	 * @ORM\ManyToOne(targetEntity="Structure")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
	 * })
	 */
	private $structure;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 * })
	 */
	private $utilisateur;
	
	
	/**
	 * @var TypeActeur
	 * @ORM\ManyToOne(targetEntity="TypeActeur")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
	 * })
	 */
	private $type;
	
	/**
	 * @var Domaine
	 * @ORM\ManyToOne(targetEntity="Domaine")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
	 * })
	 */
	private $domaine;
	
	/**
	 * @var TypeAction
	 * @ORM\ManyToOne(targetEntity="TypeAction")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
	 * })
	 */
	private $typeAction;
	
	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	public $instances;
	
    /**
     * Get id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nbAbandon
     * @param integer $nbAbandon
     * @return Synthese
     */
    public function setNbAbandon($nbAbandon) {
        $this->nbAbandon = $nbAbandon;
        return $this;
    }

    /**
     * Get nbAbandon
     * @return integer
     */
    public function getNbAbandon() {
        return $this->nbAbandon;
    }

    /**
     * Set nbDemandeAbandon
     * @param integer $nbDemandeAbandon
     * @return Synthese
     */
    public function setNbDemandeAbandon($nbDemandeAbandon) {
        $this->nbDemandeAbandon = $nbDemandeAbandon;

        return $this;
    }

    /**
     * Get nbDemandeAbandon
     * @return integer
     */
    public function getNbDemandeAbandon() {
        return $this->nbDemandeAbandon;
    }

    /**
     * Set nbEchueNonSoldee
     * @param integer $nbEchueNonSoldee
     * @return Synthese
     */
    public function setNbEchueNonSoldee($nbEchueNonSoldee) {
        $this->nbEchueNonSoldee = $nbEchueNonSoldee;
        return $this;
    }

    /**
     * Get nbEchueNonSoldee
     * @return integer
     */
    public function getNbEchueNonSoldee() {
        return $this->nbEchueNonSoldee;
    }

    /**
     * Set nbSoldeeDansLesDelais
     * @param integer $nbSoldeeDansLesDelais
     * @return Synthese
     */
    public function setNbSoldeeDansLesDelai($nbSoldeeDansLesDelais) {
        $this->nbSoldeeDansLesDelais = $nbSoldeeDansLesDelais;
        return $this;
    }

    /**
     * Get nbSoldeeDansLesDelais
     * @return integer
     */
    public function getNbSoldeeDansLesDelais() {
        return $this->nbSoldeeDansLesDelais;
    }

    /**
     * Set total
     * @param integer $total
     * @return Synthese
     */
    public function setTotal($total) {
        $this->total = $total;
        return $this;
    }

    /**
     * Get total
     *
     * @return integer
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set instance
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Synthese
     */
    public function setInstance(\Orange\MainBundle\Entity\Instance $instance = null) {
        $this->instance = $instance;
        return $this;
    }

    /**
     * Get instance
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function getInstance() {
        return $this->instance;
    }

    /**
     * Set structure
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Synthese
     */
    public function setStructure(\Orange\MainBundle\Entity\Structure $structure = null) {
        $this->structure = $structure;
        return $this;
    }

    /**
     * Get structure
     * @return \Orange\MainBundle\Entity\Structure
     */
    public function getStructure() {
        return $this->structure;
    }

    /**
     * Set utilisateur
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Synthese
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur = null) {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * Get utilisateur
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getUtilisateur() {
        return $this->utilisateur;
    }

    /**
     * Set type
     * @param \Orange\MainBundle\Entity\TypeActeur $type
     * @return Synthese
     */
    public function setType(\Orange\MainBundle\Entity\TypeActeur $type = null) {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     * @return \Orange\MainBundle\Entity\TypeActeur
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set nbNonEchue
     * @param integer $nbNonEchue
     * @return Synthese
     */
    public function setNbNonEchueNonSoldee($nbNonEchue) {
        $this->nbNonEchue = $nbNonEchue;
        return $this;
    }

    /**
     * Get nbNonEchue
     * @return integer
     */
    public function sstNbNonEchueNonSoldee() {
        return $this->nbNonEchue;
    }

    /**
     * Set nbFaiteDelai
     * @param integer $nbFaiteDelai
     * @return Synthese
     */
    public function setNbFaiteDelai($nbFaiteDelai) {
        $this->nbFaiteDelai = $nbFaiteDelai;
        return $this;
    }

    /**
     * Get nbFaiteDelai
     *
     * @return integer
     */
    public function getNbFaiteDelai() {
        return $this->nbFaiteDelai;
    }

    /**
     * Set nbFaiteHorsDelai
     * @param integer $nbFaiteHorsDelai
     * @return Synthese
     */
    public function setNbFaiteHorsDelai($nbFaiteHorsDelai) {
        $this->nbFaiteHorsDelai = $nbFaiteHorsDelai;
        return $this;
    }

    /**
     * Get nbFaiteHorsDelais
     * @return integer
     */
    public function getNbFaiteHorsDelais() {
        return $this->nbFaiteHorsDelais;
    }

    /**
     * Set nbSoldeeHorsDelais
     * @param integer $nbSoldeeHorsDelais
     * @return Synthese
     */
    public function setNbSoldeeHorsDelais($nbSoldeeHorsDelais) {
        $this->nbSoldeeHorsDelais = $nbSoldeeHorsDelais;
        return $this;
    }

    /**
     * Get nbSoldeeHorsDelai
     * @return integer
     */
    public function getNbSoldeeHorsDelai() {
        return $this->nbSoldeeHorsDelai;
    }

    /**
     * Set domaine
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     * @return Synthese
     */
    public function setDomaine(\Orange\MainBundle\Entity\Domaine $domaine = null) {
        $this->domaine = $domaine;
        return $this;
    }

    /**
     * Get domaine
     * @return \Orange\MainBundle\Entity\Domaine
     */
    public function getDomaine() {
        return $this->domaine;
    }

    /**
     * Set typeAction
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Synthese
     */
    public function setTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction = null) {
        $this->typeAction = $typeAction;
        return $this;
    }

    /**
     * Get typeAction
     * @return \Orange\MainBundle\Entity\TypeAction
     */
    public function getTypeAction() {
        return $this->typeAction;
    }

    /**
     * Set nbDemandeReport
     * @param integer $nbDemandeReport
     * @return Synthese
     */
    public function setNbDemandeReport($nbDemandeReport) {
        $this->nbDemandeReport = $nbDemandeReport;
        return $this;
    }

    /**
     * Get nbDemandeReport
     * @return integer
     */
    public function getNbDemandeReport() {
        return $this->nbDemandeReport;
    }
    
    /**
     * Set respectDelai
     * @param float $respectDelai
     * @return Synthese
     */
    public function setRespectDelai($respectDelai) {
    	$this->respectDelai = $respectDelai;
    	return $this;
    }
    
    /**
     * Get respectDelai
     * @return float
     */
    public function getRespectDelai() {
    	return $this->respectDelai;
    }
}

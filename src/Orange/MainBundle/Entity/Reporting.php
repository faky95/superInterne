<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Validator\Constraints\ActionDate as ACAssert;

/**
 * Domaine
 *
 * @ORM\Table(name="reporting")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ReportingRepository")
 */
class Reporting
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     *
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="requete", type="text",  nullable=true)
     */
    private $requete;
    
    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text",  nullable=true)
     */
    private $query;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="array_type", type="text",  nullable=true)
	 */
	private $arrayType;

	/**
	 * @return string
	 */
	public function getArrayType()
	{
		return $this->arrayType;
	}

	/**
	 * @param string $arrayType
	 */
	public function setArrayType($arrayType)
	{
		$this->arrayType = $arrayType;
	}

    /**
     * @var string
     *
     * @ORM\Column(name="parameter", type="text", nullable=true)
     */
    private $parameter;
    
    /**
     * @var string
     *
     * @ORM\Column(name="param", type="text", nullable=true)
     */
    private $param;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="iteration", type="integer", nullable=true)
     * 
     */
    private $iteration;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="type_reporting", type="integer", nullable=true)
     *
     */
    private $typeReporting;

    /**
     * @var \Pas
     *
     * @ORM\ManyToOne(targetEntity="Pas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pas_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $pas;
    
    /**
     * @var \DayOfMonth
     *
     * @ORM\ManyToOne(targetEntity="DayOfMonth")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_month_id", referencedColumnName="id")
     * })
     *
     *
     */
    private $dayOfMonth;
    
    /**
     * @var \DayOfWeek
     *
     * @ORM\ManyToOne(targetEntity="DayOfWeek")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="day_of_week_id", referencedColumnName="id")
     * })
     *
     *
     */
    private $dayOfWeek;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="reporting", cascade={"persist","merge"})
     * @ORM\JoinTable(name="reporting_has_destinataire",
     *   joinColumns={
     *     @ORM\JoinColumn(name="reporting_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    
    private $destinataire;
    
    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur", referencedColumnName="id")
     * })
     */
    private $utilisateur;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\Orange\MainBundle\Entity\Envoi", mappedBy="reporting", cascade={"persist","remove","merge"})
     */
    protected  $envoi;
    /**
    
    /**
     * Constructor
     */
    
    public function __construct()
    {
    	$this->envoi = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->destinataire = new \Doctrine\Common\Collections\ArrayCollection();
    }
	public function getId() {
		return $this->id;
	}
	public function getRequete() {
		return $this->requete;
	}
	public function setRequete($requete) {
		$this->requete = $requete;
		return $this;
	}
	public function getPas() {
		return $this->pas;
	}
	public function setPas($pas) {
		$this->pas = $pas;
		return $this;
	}
	public function getDayOfMonth() {
		return $this->dayOfMonth;
	}
	public function setDayOfMonth($dayOfMonth) {
		$this->dayOfMonth = $dayOfMonth;
		return $this;
	}
	public function getDayOfWeek() {
		return $this->dayOfWeek;
	}
	public function setDayOfWeek($dayOfWeek) {
		$this->dayOfWeek = $dayOfWeek;
		return $this;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getDestinataire() {
		return $this->destinataire;
	}
	
	/**
	 * Add user
	 *
	 * @param \Orange\MainBundle\Entity\Utilisateur $destinataire
	 * 
	 */
	public function addDestinataire($destinataire)
	{
		$this->destinataire[] = $destinataire;
	
		return $this;
	}
	
	
	public function getUtilisateur() {
		return $this->utilisateur;
	}
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	public function getIteration() {
		return $this->iteration;
	}
	public function setIteration($iteration) {
		$this->iteration = $iteration;
		return $this;
	}
	public function setDestinataire($destinataire) {
		$this->destinataire = $destinataire;
		return $this;
	}
	public function getParameter() {
		return $this->parameter;
	}
	public function setParameter($parameter) {
		$this->parameter = $parameter;
		return $this;
	}
	
	
	/**
	 * Get Envoi
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getEnvoi()
	{
		return $this->envoi;
	}
	
	/**
	 * Add Envoi
	 * @param \Orange\MainBundle\Entity\Envoi $envoi
	 * @return Reporting
	 */
	public function addEnvoi(\Orange\MainBundle\Entity\Envoi $envoi)
	{
		$this->envoi[] = $envoi;
	
		return $this;
	}
	
	/**
	 * Remove envoi
	 *
	 * @param \Orange\MainBundle\Entity\Envoi $envoi
	 */
	public function removeEnvoi(\Orange\MainBundle\Entity\Envoi $envoi)
	{
		$this->envoi->removeElement($envoi);
	}
	
	public function getLibelle() {
		return $this->libelle;
	}
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	public function getTypeReporting() {
		return $this->typeReporting;
	}
	public function setTypeReporting($typeReporting) {
		$this->typeReporting = $typeReporting;
		return $this;
	}
	public function getQuery() {
		return $this->query;
	}
	public function setQuery($query) {
		$this->query = $query;
		return $this;
	}
	public function getParam() {
		return $this->param;
	}
	public function setParam($param) {
		$this->param = $param;
		return $this;
	}
	
	
}
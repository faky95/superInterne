<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\Validator\Constraints\ActionDate as ACAssert;

/**
 * Action
 *
 * @ORM\Table(name="action")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionRepository")
 * @ACAssert
 */
class Action
{
   /** @var Structure
	*
	* @ORM\ManyToOne(targetEntity="Structure", inversedBy="action")
	* @ORM\JoinColumns({
		*   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
		* })
	*/
	private $structure;
	
	 /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string date
     *
     * @ORM\Column(name="reference", type="string", length=50, nullable=true)
     */
    private $reference;

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
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank(message="Vous devez donner une description pour cette action ! ")
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_action", type="datetime", nullable=true)
     */
    private $dateAction;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     * @Assert\NotBlank(message="Vous devez donner une date de début pour cette action ! ")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_initial", type="date", nullable=false)
     * @Assert\NotBlank(message="Vous devez donner un délai pour cette action ! ")
     */
    private $dateInitial;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionAvancement", mappedBy="action", cascade={"persist", "merge", "remove"})
     **/
    private $avancement;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionReport", mappedBy="action", cascade={"persist", "merge", "remove"})
     **/
    private $report;
	
    /**
     * @var \DateTime
     * @ORM\Column(name="date_cloture", type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateCloture;

    /**
     * @var \Priorite
     * @ORM\ManyToOne(targetEntity="Domaine", inversedBy="action")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Donnez le domaine de l'action ")
     * 
     */
    private $domaine;
    
    /**
     * @var \Priorite
     * @ORM\ManyToOne(targetEntity="Priorite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="priorite_id", referencedColumnName="id")
     * })
     */
    private $priorite;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionStatut", mappedBy="action", cascade={"persist", "merge", "remove"})
     */
    private $actionStatut;
    
    /**
     * @var \Date
     * @ORM\Column(name="date_fin_execution", type="date", nullable=true)
     */
    private $dateFinExecut;

    /**
     * @var TypeAction
     * @ORM\ManyToOne(targetEntity="TypeAction", inversedBy="action")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Donnez le type de l'action ")
     */
    private $typeAction;
    
    /**
     * @var Instance
     * @ORM\ManyToOne(targetEntity="Instance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Choisissez l'instance de l'action ")
     */
    private $instance;
    
    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="action")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="porteur_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Choisissez le porteur de l'action ")
     */
    private $porteur;
    
    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionAnimateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="animateur_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $animateur;
    
    /**
     * @ORM\OneToMany(targetEntity="Contributeur", mappedBy="action", cascade={"persist", "merge" ,"remove"})
     */
    private $contributeur;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Groupe", inversedBy="action", cascade={"persist", "merge"})
     * @ORM\JoinTable(name="action_has_groupe",
     *   joinColumns={
     *     @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="groupe_id", referencedColumnName="id")
     *   }
     * )
     */
    private $groupe;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Signalisation", inversedBy="action")
     * @ORM\JoinTable(name="action_has_signalisation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="signalisation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $signalisation;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean", nullable=true)
     */
    private  $isDeleted;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isReload", type="boolean", nullable=true)
     */
    private  $isReload;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionCyclique", mappedBy="action", cascade={"persist"})
     **/
    private $actionCyclique;
        
    /**
     * @var string
     * @ORM\Column(name="etat_courant", type="string", length=255, nullable=true)
     */
    private $etatCourant;
    
    /**
     * @var string
     * @ORM\Column(name="etat_reel", type="string", length=255, nullable=true)
     */
    private $etatReel;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Document", mappedBy="action", cascade={"persist"})
     */
    private $document;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tmp_contributeur;
    
    /**
     * @var \Orange\MainBundle\Entity\Document
     */
    private $erq;
    
    /**
     * @var \Orange\MainBundle\Entity\Statut
     * 
     */
    private $statutChange;

    public $toDebut;
    
    public $fromDebut;
    
    public $toInitial;
    
    public $fromInitial;
    
    public $toCloture;
    
    public $fromCloture;
    
    public $statut;
    
    public $instances;
    
	public function __construct(){
		$this->dateAction = new \DateTime();
		$this->isDeleted = 0;
		$this->actionCyclique = new \Doctrine\Common\Collections\ArrayCollection();
		$this->isReload = false;
		$this->contributeur = new ArrayCollection();
		$this->signalisation = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tmp_contributeur  = new ArrayCollection();
		$this->groupe = new ArrayCollection();
		$this->reference = "ACTION_".$this->getId().strtoupper(ActionUtils::random(10));
		$this->etatCourant = Statut::ACTION_NOUVELLE;
		$this->etatReel = Statut::ACTION_NOUVELLE;
		$this->document = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set reference
     *
     * @param string $reference
     * @return Action
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Action
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Action
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateAction
     *
     * @param \DateTime $dateAction
     * @return Action
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime 
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Action
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateInitial
     *
     * @param \DateTime $dateInitial
     * @return Action
     */
    public function setDateInitial($dateInitial)
    {
    	$today = new \DateTime();
    	if ($dateInitial > $today){
    		$this->etatCourant= 'ACTION_NON_ECHUE';
    		$this->etatReel= 'ACTION_NON_ECHUE';
    	}
        $this->dateInitial = $dateInitial;

        return $this;
    }

    /**
     * Get dateInitial
     *
     * @return \DateTime 
     */
    public function getDateInitial()
    {
        return $this->dateInitial;
    }

    /**
     * Set dateCloture
     *
     * @param \DateTime $dateCloture
     * @return Action
     */
    public function setDateCloture($dateCloture)
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    /**
     * Get dateCloture
     *
     * @return \DateTime 
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }

    /**
     * Set priorite
     *
     * @param \Orange\MainBundle\Entity\Priorite $priorite
     * @return Action
     */
    public function setPriorite(\Orange\MainBundle\Entity\Priorite $priorite = null)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return \Orange\MainBundle\Entity\Priorite 
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set typeAction
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Action
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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Action
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     * @return Action
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
     * Set porteur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $porteur
     * @return Action
     */
    public function setPorteur(\Orange\MainBundle\Entity\Utilisateur $porteur = null)
    {
        $this->porteur = $porteur;
        return $this;
    }

    /**
     * Get porteur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getPorteur()
    {
        return $this->porteur;
    }

    /**
     * Add contributeur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $contributeur
     * @return Action
     */
    public function addContributeur(\Orange\MainBundle\Entity\Utilisateur $contributeur)
    {
        $this->contributeur[] = $contributeur;

        return $this;
    }

    /**
     * Remove contributeur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $contributeur
     */
    public function removeContributeur(\Orange\MainBundle\Entity\Utilisateur $contributeur)
    {
        $this->contributeur->removeElement($contributeur);
    }

    /**
     * Get contributeur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContributeur()
    {
        return $this->contributeur;
    }

    /**
     * Get E-mail contributeurs
     * @return array
     */
    public function getEmailContributeurs() {
    	$data = array();
    	foreach($this->contributeur as $contributeur) {
    		$data[] = $contributeur->getUtilisateur()->getEmail();
    	}
        return $data;
    }

    /**
     * Add groupe
     *
     * @param \Orange\MainBundle\Entity\Groupe $groupe
     * @return Action
     */
    public function addGroupe(\Orange\MainBundle\Entity\Groupe $groupe)
    {
        $this->groupe[] = $groupe;

        return $this;
    }

    /**
     * Remove groupe
     *
     * @param \Orange\MainBundle\Entity\Groupe $groupe
     */
    public function removeGroupe(\Orange\MainBundle\Entity\Groupe $groupe)
    {
        $this->groupe->removeElement($groupe);
    }

    /**
     * Get groupe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupe()
    {
        return $this->groupe;
    }
    
    /**
     * Get tmp_contributeur
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpContributeur() {
    	$this->tmp_contributeur = new ArrayCollection();
    	foreach($this->contributeur as $membre) {
    		$this->tmp_contributeur->add($membre->getUtilisateur());
    	}
    	return $this->tmp_contributeur;
    }
    
    /**
     * @param Utilisateur $tmp_contributeur
     * @return \Orange\MainBundle\Entity\Action
     */
    public function addTmpContributeur($tmp_contributeur) {
    	$this->tmp_contributeur->add($tmp_contributeur);
    	$isExist=false;
    	foreach ($this->contributeur as $membre){
    		if($membre->getUtilisateur()->getId()==$tmp_contributeur->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$membre= new Contributeur();
    		$membre->setAction($this);
    		$membre->setUtilisateur($tmp_contributeur);
    		$this->contributeur->add($membre);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_contributeur
     * @return \Orange\MainBundle\Entity\Action
     */
    public function removeTmpContributeur($tmp_contributeur) {
    	$idMembre = null;
    	foreach ($this->contributeur as $cont){
    		if($cont->getUtilisateur()->getId()==$cont->getId()) {
    			$idMembre=$cont;
    			break;
    		}
    	}
    	if ($idMembre!==null) {
    		$this->contributeur->removeElement($idMembre);
    	}
    	return $this;
    }
    
    

    /**
     * Add actionStatut
     *
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     * @return Action
     */
    public function addActionStatut(\Orange\MainBundle\Entity\ActionStatut $actionStatut)
    {
    	$actionStatut->setAction($this);
        $this->actionStatut[] = $actionStatut;

        return $this;
    }

    /**
     * Remove actionStatut
     *
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     */
    public function removeActionStatut(\Orange\MainBundle\Entity\ActionStatut $actionStatut)
    {
        $this->actionStatut->removeElement($actionStatut);
    }

    /**
     * Get actionStatut
     *
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getActionStatut()
    {
        return $this->actionStatut;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Action
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
     * Get espace
     * @return \Orange\MainBundle\Entity\Espace 
     */
    public function getEspace()
    {
        return $this->instance ? $this->instance->getEspace() : null;
    }

    /**
     * Get id espace
     * @return number
     */
    public function getEspaceId()
    {
        return $this->getEspace() ? $this->getEspace()->getId() : null;
    }

    /**
     * Add signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     * @return Action
     */
    public function addSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation[] = $signalisation;

        return $this;
    }

    /**
     * Remove signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     */
    public function removeSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation->removeElement($signalisation);
    }

    /**
     * Get signalisation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignalisation()
    {
        return $this->signalisation;
    }
    
    public function __toString(){
    	
    	return $this->libelle." ";
    	
    }

    /**
     * Set isReload
     *
     * @param boolean $isReload
     * @return Action
     */
    public function setIsReload($isReload)
    {
        $this->isReload = $isReload;

        return $this;
    }

    /**
     * Get isReload
     *
     * @return boolean 
     */
    public function getIsReload()
    {
        return $this->isReload;
    }

    /**
     * Add avancement
     *
     * @param \Orange\MainBundle\Entity\ActionAvancement $avancement
     * @return Action
     */
    public function addAvancement(\Orange\MainBundle\Entity\ActionAvancement $avancement)
    {
        $this->avancement[] = $avancement;

        return $this;
    }

    /**
     * Remove avancement
     *
     * @param \Orange\MainBundle\Entity\ActionAvancement $avancement
     */
    public function removeAvancement(\Orange\MainBundle\Entity\ActionAvancement $avancement)
    {
        $this->avancement->removeElement($avancement);
    }

    /**
     * Get avancement
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvancement()
    {
        return $this->avancement;
    }

    /**
     * Add report
     *
     * @param \Orange\MainBundle\Entity\ActionReport $report
     * @return Action
     */
    public function addReport(\Orange\MainBundle\Entity\ActionReport $report)
    {
        $this->report[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \Orange\MainBundle\Entity\ActionReport $report
     */
    public function removeReport(\Orange\MainBundle\Entity\ActionReport $report)
    {
        $this->report->removeElement($report);
    }

    /**
     * Get report
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Add actionCyclique
     *
     * @param \Orange\MainBundle\Entity\ActionCyclique $actionCyclique
     * @return Action
     */
    public function addActionCyclique(\Orange\MainBundle\Entity\ActionCyclique $actionCyclique)
    {
        $this->actionCyclique[] = $actionCyclique;

        return $this;
    }

    /**
     * Remove actionCyclique
     *
     * @param \Orange\MainBundle\Entity\ActionCyclique $actionCyclique
     */
    public function removeActionCyclique(\Orange\MainBundle\Entity\ActionCyclique $actionCyclique)
    {
        $this->actionCyclique->removeElement($actionCyclique);
    }

    /**
     * Get actionCyclique
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActionCyclique()
    {
        return $this->actionCyclique;
    }
    

    /**
     * Set etatCourant
     *
     * @param string $etatCourant
     * @return Action
     */
    public function setEtatCourant($etatCourant)
    {
        $this->etatCourant = $etatCourant;
        return $this;
    }
    
    public function getEtatCourant() {
        return $this->etatCourant;
    }
    
    public function hasToDebut(){
    	return $this->toDebut;
    }
    
    public function hasFromDebut(){
    	return $this->fromDebut;
    
    }
    
    public function hasToInitial(){
    	return $this->toInitial;
    }
    
    public function hasFromInitial(){
    	return $this->fromInitial;
    }
    
    public function hasToCloture(){
    	return $this->toCloture;
    }
    
    public function hasFromCloture(){
    	return $this->fromCloture;
    }
    
    public function hasStatut(){
    	return $this->statut;
    }

    /**
     * Set animateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $animateur
     *
     * @return Action
     */
    public function setAnimateur(\Orange\MainBundle\Entity\Utilisateur $animateur = null)
    {
        $this->animateur = $animateur;
        return $this;
    }

    /**
     * Get animateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getAnimateur()
    {
        return $this->animateur;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Document $document
     * @param integer $type
     * @return \Orange\MainBundle\Entity\Action
     */
    public function addDocument($document, $type = null) {
    	if($type) {
    		$document->setType($type);
    	}
    	$document->setAction($this);
    	$this->document->add($document);
    	return $this;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getDocument() {
		return $this->document;
	}
	
	/**
	 * @return \Orange\MainBundle\Entity\Document
	 */
	public function getErq() {
		return $this->erq;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Document $erq
	 * @return \Orange\MainBundle\Entity\Action
	 */
	public function setErq($erq) {
		$this->erq = $erq;
		return $this;
	}
	/**
	 * Get structure
	 *
	 * @return \Orange\MainBundle\Entity\Structure
	 */
	public function getStructure() {
		return $this->structure;
	}
	
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}
	
	

    /**
     * Set etatReel
     *
     * @param string $etatReel
     *
     * @return Action
     */
    public function setEtatReel($etatReel)
    {
        $this->etatReel = $etatReel;

        return $this;
    }

    /**
     * Get etatReel
     *
     * @return string
     */
    public function getEtatReel()
    {
        return $this->etatReel;
    }
    
    /**
     * Remove document
     *
     * @param \Orange\MainBundle\Entity\Document $document
     */
    public function removeDocument(\Orange\MainBundle\Entity\Document $document)
    {
        $this->document->removeElement($document);
    }
	public function getDateFinExecut() {
		return $this->dateFinExecut;
	}
	public function setDateFinExecut($dateFinExecut) {
		$this->dateFinExecut = $dateFinExecut;
		return $this;
	}
	public function getStatutChange() {
		return $this->statutChange;
	}
	public function setStatutChange($statutChange) {
		$this->statutChange = $statutChange;
		return $this;
	}
	public function getAllContributeur(){
		$cont = "";
		$j=1;
		foreach ($this->getContributeur() as $contributeur){
			$cont = $cont."\n".$j.'. '.$contributeur->getUtilisateur()->getCompletNom();
			$j++;
		}
		return $cont;
	}
	public function getAllAvancement(){
		$ava = "";
		$z = 1;
		foreach ($this->getAvancement() as $avancement){
			$ava = $ava."\n".$z.') '.$avancement->getDescription();
			$z++;
		}
		return $ava;
	}
	
}

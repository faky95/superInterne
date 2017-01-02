<?php
namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Espace
 *
 * @ORM\Table(name="espace")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\EspaceRepository")
 */
class Espace
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
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 * 
	 * @Assert\NotBlank()
	 * 
	 */
	private $libelle;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", nullable=false)
	 */
	private $description;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_creation", type="datetime", nullable=false)
	 */
	private $dateCreation;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="etat", type="integer", nullable=true)
	 */
	private $etat;

	/**
	 * 
	 * @ORM\OneToMany(targetEntity="MembreEspace", orphanRemoval=true,mappedBy="espace", cascade={"persist","remove","merge"})
	 */
	private $membreEspace;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	private $tmp_membre;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="isDeleted", type="boolean", nullable=true)
	 */
	private  $isDeleted;
	
	
	/**
	 * 
	 * @var Instance
	 * @ORM\OneToOne(targetEntity="Instance", inversedBy="espace", cascade={"persist","remove","merge"})
	 */
	private $instance;
	
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Domaine", inversedBy="espace", cascade={"persist", "merge", "remove"})
	 * @ORM\JoinTable(name="espace_has_domaine",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="espace_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	private $domaine;
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="TypeAction", inversedBy="espace", cascade={"persist", "merge", "remove"})
	 * @ORM\JoinTable(name="espace_has_type_action",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="espace_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	private $typeAction;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->membreEspace = new ArrayCollection();
		$this->tmp_membre = new ArrayCollection();
	    $this->dateCreation = new \DateTime();
	    $this->isDeleted=false;
	}
	
	public function __toString(){
		return $this->libelle;
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
	 * Set libelle
	 *
	 * @param string $libelle
	 * @return Espace
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
	 * @return Espace
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
	 * Set dateCreation
	 *
	 * @param \DateTime $dateCreation
	 * @return Espace
	 */
	public function setDateCreation($dateCreation)
	{
		$this->dateCreation = $dateCreation;
		return $this;
	}

	/**
	 * Get dateCreation
	 *
	 * @return \DateTime
	 */
	public function getDateCreation()
	{
		return $this->dateCreation;
	}
	
	/**
	 * Set etat
	 *
	 * @param integer $etat
	 * @return Espace
	 */
	public function setEtat($etat)
	{
		$this->etat = $etat;

		return $this;
	}

	/**
	 * Get etat
	 *
	 * @return integer
	 */
	public function getEtat()
	{
		return $this->etat;
	}
	
	/**
	 * Add membres
	 *
	 * @param \Orange\MainBundle\Entity\membre $membres
	 * @return Espace
	 */
	public function addMembreEspace(\Orange\MainBundle\Entity\MembreEspace $membres)
	{
		$this->membreEspace[] = $membres;
		return $this;
	}

	/**
	 * Remove membres
	 *
	 * @param \Orange\MainBundle\Entity\membre $membres
	 */
	public function removeMembreEspace(\Orange\MainBundle\Entity\MembreEspace $membres)
	{
		$this->membreEspace->removeElement($membres);
	}

	/**
	 * Get membreEspace
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getMembreEspace()
	{
		return $this->membreEspace;
	}
	
	 /**
     * Get tmp_membre
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
	public function getTmpMembre() {
		$this->tmp_membre = new ArrayCollection();
		foreach($this->membreEspace as $membre) {
			$this->tmp_membre->add($membre->getUtilisateur());
		}
		return $this->tmp_membre;
	}
	
	/**
	 * @param Utilisateur $tmp_membre
	 * @return \Orange\MainBundle\Entity\Espace
	 */
	public function addTmpMembre($tmp_membre) {
		$this->tmp_membre->add($tmp_membre);
		$isExist=false;
		foreach ($this->membreEspace as $membre){
			if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
				$isExist=true; 
				break;
			}
		}
		if ($isExist==false) {
			$membre= new MembreEspace();
			$membre->setEspace($this);
			$membre->setUtilisateur($tmp_membre);
			$this->membreEspace->add($membre);
		}
		return $this;
	}
	
	/**
	 * @param Utilisateur $tmp_membre
	 * @return \Orange\MainBundle\Entity\Espace
	 */
	public function removeTmpMembre($tmp_membre) {
		$idMembre = null;
		foreach ($this->membreEspace as $membre){
			if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
				$idMembre=$membre;
				break;
			}
		}
		if ($idMembre!==null) {
			$this->membreEspace->removeElement($idMembre);
		}
		return $this;
	}
	
	public function getIsDeleted() {
		return $this->isDeleted;
	}
	
	public function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
		return $this;
	}
    

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     *
     * @return Espace
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
     * Add domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     * @return Instance
     */
    public function addDomaine(\Orange\MainBundle\Entity\Domaine $domaine)
    {
    	$this->domaine[] = $domaine;
    
    	return $this;
    }
    
    /**
     * Remove domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     */
    public function removeDomaine(\Orange\MainBundle\Entity\Domaine $domaine)
    {
    	$this->domaine->removeElement($domaine);
    }
    
    /**
     * Get domaine
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDomaine()
    {
    	return $this->domaine;
    }
    
    /**
     * Add domaine
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Instance
     */
    public function addTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
    	$this->typeAction[] = $typeAction;
    
    	return $this;
    }
    
    /**
     * Remove typeAction
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     */
    public function removeTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
    	$this->typeAction->removeElement($typeAction);
    }
    
    /**
     * Get typeAction
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypeAction()
    {
    	return $this->typeAction;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Utilisateur $user
     * @return boolean
     */
    public function isGestionnaire($user = null) {
    	$data =$this->membreEspace->filter(function($membre) use($user) {
    			return $membre->getUtilisateur()->getId()==$user->getId() && $membre->getIsGestionnaire();
    		});
    	return $data->count() ? true : false;
    }
    
    /**
     * @return number
     */
    public function getTotalNumber() {
    	return $this->instance->getAction()->filter(function($action) { return !strpos($action->getEtatCourant(), 'ARCHIVE'); })->count();
    }
    
    /**
     * @param Utilisateur $user 
     * @return number
     */
    public function numberAction($user) {
    	if($user==null) {
    		return 0;
    	}
    	return $this->instance->getAction()->filter(function($action) use($user) {
    			return $action->getPorteur()==$user && !strpos($action->getEtatCourant(), 'ARCHIVE');
    		})->count();
    }
    
}

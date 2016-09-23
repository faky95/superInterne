<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * Chantier
 *
 * @ORM\Table(name="chantier")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ChantierRepository")
 */
class Chantier
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
	 * @var \Projet
	 *
	 * @ORM\ManyToOne(targetEntity="Projet", inversedBy="chantierProjet")
	 * @ORM\JoinColumn(nullable=false)
	 * 
	 * @Assert\NotBlank()
	 */
	private $projet;
	
	/**
	 * @var \Instance
	 *
	 * @ORM\OneToOne(targetEntity="Instance", inversedBy="chantier")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
	 * })
	 */
	private $instance;
	
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
	 * @var boolean
	 *
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat;
	
	/**
	 *
	 * @ORM\OneToMany(targetEntity="MembreChantier", mappedBy="chantier", cascade={"persist","remove","merge"})
	 */
	private $membreChantier;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 */
	private $tmp_membre;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="isDeleted", type="boolean", nullable=false)
	 */
	private  $isDeleted;
	
	public function __construct(){
		$this->membreChantier=new  ArrayCollection();
		$this->tmp_membre = new ArrayCollection();
		$this->dateCreation = new \DateTime();
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
     * @return Chantier
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
     * @return Chantier
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
     * @return Chantier
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
     * @param boolean $etat
     * @return Chantier
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set projet
     *
     * @param \Orange\MainBundle\Entity\Projet $projet
     * @return Chantier
     */
    public function setProjet(\Orange\MainBundle\Entity\Projet $projet = null)
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

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Chantier
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
     * Add membres
     *
     * @param \Orange\MainBundle\Entity\membreChantier $membres
     * @return Chantier
     */
    public function addMembreEspace(\Orange\MainBundle\Entity\MembreChantier $membres)
    {
    	$this->membreChantier[] = $membres;
    	return $this;
    }
    /**
     * Remove membres
     *
     * @param \Orange\MainBundle\Entity\membre $membres
     */
    public function removeMembreChantier(\Orange\MainBundle\Entity\MembreChantier $membres)
    {
    	$this->membreChantier->removeElement($membres);
    }
    
    
    /**
     * Get membreChantier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMembreChantier()
    {
    	return $this->membreChantier;
    }
    
    /**
     * Get tmp_membre
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpMembre() {
    	$this->tmp_membre = new ArrayCollection();
    	foreach($this->membreChantier as $membre) {
    		$this->tmp_membre->add($membre->getUtilisateur());
    	}
    	return $this->tmp_membre;
    }
    
    /**
     * @param Utilisateur $tmp_membre
     * @return \Orange\MainBundle\Entity\Chantier
     */
    public function addTmpMembre($tmp_membre) {
    	$this->tmp_membre->add($tmp_membre);
    	$isExist=false;
    	foreach ($this->membreChantier as $membre){
    		if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$membre= new MembreChantier();
    		$membre->setChantier($this);
    		$membre->setUtilisateur($tmp_membre);
    		$this->membreChantier->add($membre);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_membre
     * @return \Orange\MainBundle\Entity\Chantier
     */
    public function removeTmpMembre($tmp_membre) {
    	$idMembre = null;
    	foreach ($this->membreChantier as $membre){
    		if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
    			$idMembre=$membre;
    			break;
    		}
    	}
    	if ($idMembre!==null) {
    		$this->membreChantier->removeElement($idMembre);
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
     * Add membreChantier
     *
     * @param \Orange\MainBundle\Entity\MembreChantier $membreChantier
     * @return Chantier
     */
    public function addMembreChantier(\Orange\MainBundle\Entity\MembreChantier $membreChantier)
    {
        $this->membreChantier[] = $membreChantier;

        return $this;
    }
}

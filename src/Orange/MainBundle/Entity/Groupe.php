<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe", indexes={@ORM\Index(name="structure_id", columns={"structure_id"})})
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\GroupeRepository")
 */
class Groupe
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
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     * 
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text",  nullable=true)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @var \Structure
     *
     * @ORM\ManyToOne(targetEntity="Structure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     * })
     */
    private $structure;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Action", mappedBy="groupe")
     */
    private $action;
    
    
    /**
     *
     * @ORM\OneToMany(targetEntity="MembreGroupe", mappedBy="groupe", cascade={"persist","remove","merge"})
     */
    private $membreGroupe;
    
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
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
        $this->membreGroupe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tmp_membre = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->isDeleted=false;
    }

    public function __toString(){
    	return $this->name." ";
    }
    
	public function getId() {
		return $this->id;
	}
    

    /**
     * Set name
     *
     * @param string $name
     * @return Groupe
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
	
    /**
     * Set email
     *
     * @param string $email
     * @return Groupe
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Groupe
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Groupe
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Groupe
     */
    public function setStructure(\Orange\MainBundle\Entity\Structure $structure)
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
     * Add membre
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Groupe
     */
    public function addMembreGroupe(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->membreGroupe[] = $utilisateur;

        return $this;
    }

    /**
     * Remove membre
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     */
    public function removeMembreGroupe(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->membreGroupe->removeElement($utilisateur);
    }

    /**
     * Get utilisateur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembreGroupe()
    {
        return $this->membreGroupe;
    }
    
    
	public function getIsDeleted() {
		return $this->isDeleted;
	}
	public function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
		return $this;
	}
	
	
	/**
	 * Get tmp_membre
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getTmpMembre() {
		$this->tmp_membre = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($this->membreGroupe as $membre) {
			$this->tmp_membre->add($membre->getUtilisateur());
		}
		return $this->tmp_membre;
	}
	
	/**
	 * @param Utilisateur $tmp_membre
	 * @return \Orange\MainBundle\Entity\Groupe
	 */
	public function addTmpMembre($tmp_membre) {
		$this->tmp_membre->add($tmp_membre);
		$isExist=false;
		foreach ($this->membreGroupe as $membre){
			if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
				$isExist=true;
				break;
			}
		}
		if ($isExist==false) {
			$membre= new MembreGroupe();
			$membre->setGroupe($this);
			$membre->setUtilisateur($tmp_membre);
			$this->membreGroupe->add($membre);
		}
		return $this;
	}
	
	/**
	 * @param Utilisateur $tmp_membre
	 * @return \Orange\MainBundle\Entity\Groupe
	 */
	public function removeTmpMembre($tmp_membre) {
		$idMembre = null;
		foreach ($this->membreGroupe as $membre){
			if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
				$idMembre=$membre;
				break;
			}
		}
		if ($idMembre!==null) {
			$this->membreGroupe->removeElement($idMembre);
		}
		return $this;
	}
    

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return Groupe
     */
    public function addAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action[] = $action;

        return $this;
    }

    /**
     * Remove action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     */
    public function removeAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action->removeElement($action);
    }

    /**
     * Get action
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAction()
    {
        return $this->action;
    }
}

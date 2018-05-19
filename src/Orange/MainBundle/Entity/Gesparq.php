<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gesparq
 * @ORM\Table(name="gesparq")
 * @ORM\Entity
 */
class Gesparq
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     * @Assert\NotNull()
     */
    private $libelle;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Bu")
     * @ORM\JoinTable(name="gp_has_bu",
     *   joinColumns={
     *     @ORM\JoinColumn(name="gp_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Bu_id", referencedColumnName="id")
     *   }
     * )
     */
    private $bu;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur")
     * @ORM\JoinTable(name="gp_has_admin",
     *   joinColumns={
     *     @ORM\JoinColumn(name="gp_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
     */
    private $admin;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur")
     * @ORM\JoinTable(name="gp_has_Auditeur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="gp_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
     */
    private $auditeur;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->admin = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->auditeur = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->bu = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString(){
    	return $this->libelle;
    }
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * @param string $libelle
	 * @return Bu
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * get bu
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getBu() {
		return $this->bu;
	}
	
	/**
	 * Add bu
	 * @param \Orange\MainBundle\Entity\Bu $bu
	 * @return Gesparq
	 */
	public function addBu(\Orange\MainBundle\Entity\Bu $bu) {
		$bu->addBu($this);
		$this->bu[] = $bu;
		return $this;
	}
	
	/**
	 * Remove bu
	 * @param \Orange\MainBundle\Entity\Bu $utilisateur
	 * @return Gesparq
	 */
	public function removeBu(\Orange\MainBundle\Entity\Bu $bu) {
		$this->bu->removeElement($bu);
	}
	
	/**
	 * get admin
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getAdmin() {
		return $this->admin;
	}
	
    /**
     * Add admin
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Gesparq
     */
	public function addAdmin(\Orange\MainBundle\Entity\Utilisateur $utilisateur) {
		$utilisateur->addGpAdmin($this);
		$this->admin[] = $utilisateur;
        return $this;
    }

    /**
     * Remove admin
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Gesparq
     */
    public function removeAdmin(\Orange\MainBundle\Entity\Utilisateur $utilisateur) {
    	$this->admin->removeElement($utilisateur);
    }
    
    /**
     * @return auditeur
     */
    public function getAuditeur() {
    	return $this->auditeur;
    }
    
    /**
     * Add auditeur
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Gesparq
     */
    public function addAuditeur(\Orange\MainBundle\Entity\Utilisateur $utilisateur) {
    	$utilisateur->addGpAuditeur($this);
    	$this->auditeur[] = $utilisateur;
    	return $this;
    }
    
    /**
     * Remove auditeur
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Gesparq
     */
    public function removeAuditeur(\Orange\MainBundle\Entity\Utilisateur$utilisateur) {
    	$this->auditeur->removeElement($utilisateur);
    }
    
}

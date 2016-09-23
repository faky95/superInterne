<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Projet
 *
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ProjetRepository")
 */
class Projet
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
     * 
     * @Assert\NotNull(message="Veuillez renseigner le libelle du projet , SVP")
     * 
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var \Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="projets")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chef_projet", referencedColumnName="id")
     * })
     * 
     * @Assert\NotNull(message="Veuillez choisir un  chef de projet , SVP")
     * 
     */
    private $chefProjet;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Chantier", mappedBy="projet", cascade={"persist","remove","merge"})
     */
    private $chantierProjet;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Domaine", inversedBy="projet", cascade={"persist","remove","merge"})
     * @ORM\JoinTable(name="projet_has_domaine",
     *   joinColumns={
     *     @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="Domaine", inversedBy="projet", cascade={"persist","remove","merge"})
     * @ORM\JoinTable(name="projet_has_type_action",
     *   joinColumns={
     *     @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
     *   }
     * )
     */
    private $typeAction;
    
    
    /**
	 *
	 * @ORM\OneToMany(targetEntity="MembreProjet", mappedBy="projet", cascade={"persist","remove","merge"})
	 */
    private $membreProjet;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean", nullable=false)
     */
    private  $isDeleted;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tmp_membre;
    
    public function __construct(){
    $this->membreProjet=new ArrayCollection();
    $this->domaine=new ArrayCollection();
    $this->typeAction=new ArrayCollection();
    $this->chantierProjet=new ArrayCollection();
	$this->dateCreation = new \DateTime();    	
	$this->isDeleted = false;
    }
		
    public function __toString(){
    	return $this->libelle;
    }
    
	public function getId() {
		return $this->id;
	}
	
	public function getLibelle() {
		return $this->libelle;
	}
	
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
    /**
     * Set description
     *
     * @param string $description
     * @return Projet
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
     * @return Projet
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
     * Set chefProjet
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $chefProjet
     * @return Projet
     */
    public function setChefProjet(\Orange\MainBundle\Entity\Utilisateur $chefProjet = null)
    {
        $this->chefProjet = $chefProjet;

        return $this;
    }
	
    /**
     * Get chefProjet
     *
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getChefProjet()
    {
        return $this->chefProjet;
    }
    /**
     * Add chantier
     *
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     * @return Chantier
     */
    public function addChantierProjet(\Orange\MainBundle\Entity\Chantier $chantier)
    {
    	$this->chantierProjet[] = $chantier;
    	return $this;
    }
    /**
     * Remove chantier
     *
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     */
    public function removeChantierProjet(\Orange\MainBundle\Entity\Chantier $chantier)
    {
    	$this->chantierProjet->removeElement($chantier);
    }
    
    
    /**
     * Get membreChantier
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChantierProjet()
    {
    	return $this->chantierProjet;
    }
	public function getIsDeleted() {
		return $this->isDeleted;
	}
	public function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
		return $this;
	}
	
    
    

    /**
     * Add domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     * @return Projet
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
     * Add membres
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $membres
     * @return Projet
     */
    public function addMembre(\Orange\MainBundle\Entity\Utilisateur $membres)
    {
        $this->membres[] = $membres;

        return $this;
    }

    /**
     * Remove membres
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $membres
     */
    public function removeMembre(\Orange\MainBundle\Entity\Utilisateur $membres)
    {
        $this->membres->removeElement($membres);
    }

    /**
     * Get membres
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembres()
    {
        return $this->membres;
    }
    /**
     * Get tmp_membre
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpMembre() {
    	$this->tmp_membre = new ArrayCollection();
    	foreach($this->membreProjet as $membre) {
    		$this->tmp_membre->add($membre->getUtilisateur());
    	}
    	return $this->tmp_membre;
    }
    
    /**
     * @param Utilisateur $tmp_membre
     * @return \Orange\MainBundle\Entity\Projet
     */
    public function addTmpMembre($tmp_membre) {
    	$this->tmp_membre->add($tmp_membre);
    	$isExist=false;
    	foreach ($this->membreProjet as $membre){
    		if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$membre= new MembreProjet();
    		$membre->setProjet($this);
    		$membre->setUtilisateur($tmp_membre);
    		$this->membreProjet->add($membre);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_membre
     * @return \Orange\MainBundle\Entity\Projet
     */
    public function removeTmpMembre($tmp_membre) {
    	$idMembre = null;
    	foreach ($this->membreProjet as $membre){
    		if($membre->getUtilisateur()->getId()==$tmp_membre->getId()) {
    			$idMembre=$membre;
    			break;
    		}
    	}
    	if ($idMembre!==null) {
    		$this->membreProjet->removeElement($idMembre);
    	}
    	return $this;
    }

    /**
     * Add typeAction
     *
     * @param \Orange\MainBundle\Entity\Domaine $typeAction
     * @return Projet
     */
    public function addTypeAction(\Orange\MainBundle\Entity\Domaine $typeAction)
    {
        $this->typeAction[] = $typeAction;

        return $this;
    }

    /**
     * Remove typeAction
     *
     * @param \Orange\MainBundle\Entity\Domaine $typeAction
     */
    public function removeTypeAction(\Orange\MainBundle\Entity\Domaine $typeAction)
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
     * Add membreProjet
     *
     * @param \Orange\MainBundle\Entity\MembreProjet $membreProjet
     * @return Projet
     */
    public function addMembreProjet(\Orange\MainBundle\Entity\MembreProjet $membreProjet)
    {
        $this->membreProjet[] = $membreProjet;

        return $this;
    }

    /**
     * Remove membreProjet
     *
     * @param \Orange\MainBundle\Entity\MembreProjet $membreProjet
     */
    public function removeMembreProjet(\Orange\MainBundle\Entity\MembreProjet $membreProjet)
    {
        $this->membreProjet->removeElement($membreProjet);
    }

    /**
     * Get membreProjet
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembreProjet()
    {
        return $this->membreProjet;
    }
}

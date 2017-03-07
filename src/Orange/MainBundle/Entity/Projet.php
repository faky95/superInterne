<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Projet
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
	
    /**
     * @var string
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotNull(message="Veuillez renseigner le libelle du projet , SVP")
     */
    private $libelle;
    
    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="projet", cascade={"persist","remove","merge"})
     * @ORM\JoinTable(name="chef_projet",
     *   joinColumns={
     *     @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
     */
    private $chefProjet;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_creation", type="datetime", nullable=false)
     */
    private $dateCreation;
    
    /**
     * @ORM\OneToMany(targetEntity="Chantier", mappedBy="projet", cascade={"persist","remove","merge"})
     */
    private $chantier;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @var \Doctrine\Common\Collections\ArrayCollection
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
     * @var boolean
     * @ORM\Column(name="etat", type="boolean", nullable=false)
     */
    private  $etat = true;
    

    public function __construct() {
	    $this->chefProjet = new ArrayCollection();
	    $this->domaine = new ArrayCollection();
	    $this->typeAction = new ArrayCollection();
	    $this->chantier = new ArrayCollection();
		$this->dateCreation = new \DateTime('NOW');    	
    }
		
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->libelle;
    }
    
    /**
     * get id
     * @return number
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * get libelle
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * set libelle
	 * @param string $libelle
	 * @return \Orange\MainBundle\Entity\Projet
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
    /**
     * Set description
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
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
	
    /**
     * Set dateCreation
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
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }
	
    /**
     * Add chantier
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     * @return Chantier
     */
    public function addChantier(\Orange\MainBundle\Entity\Chantier $chantier)
    {
    	$chantier->setProjet($this);
    	$this->chantier[] = $chantier;
    	return $this;
    }
    
    /**
     * Remove chantier
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     */
    public function removeChantier(\Orange\MainBundle\Entity\Chantier $chantier)
    {
    	$this->chantier->removeElement($chantier);
    }
    
    /**
     * Get chantier
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChantier()
    {
    	return $this->chantier;
    }

    /**
     * Add domaichefProjetne
     * @param \Orange\MainBundle\Entity\Domaine $chefProjet
     * @return Projet
     */
    public function addChefProjet(\Orange\MainBundle\Entity\Utilisateur $chefProjet)
    {
        $this->chefProjet[] = $chefProjet;
        return $this;
    }

    /**
     * Remove chefProjet
     * @param \Orange\MainBundle\Entity\Domaine $chefProjet
     */
    public function removeChefProjet(\Orange\MainBundle\Entity\Utilisateur $chefProjet)
    {
        $this->chefProjet->removeElement($chefProjet);
    }

    /**
     * Get chefProjet
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getChefProjet()
    {
    	return $this->chefProjet;
    }

    /**
     * list chefChantier
     * @return string
     */
     public function listChefProjet() {
    	$str = null;
    	if($this->chefProjet->count()==0) {
    		$str = 'Aucun';
    	} elseif($this->chefProjet->count()==1) {
    		$str = $this->chefProjet->first()->__toString();
    	} elseif($this->chefProjet->count()==2) {
    		$str = sprintf('%s et %s', $this->chefProjet->first()->__toString(), $this->chefProjet->last());
    	} else {
    		$str = sprintf('%s, %s ...', $this->chefProjet->first(), $this->chefProjet->get(1));
    	}
    	return $str;
    }

    /**
     * list all chefProjet
     * @return string
     */
     public function listAllChefProjet() {
    	$str = null;
    	if($this->chefProjet->count()==0) {
    		$str = 'Aucun';
    	} elseif($this->chefProjet->count()==1) {
    		$str = $this->chefProjet->first()->__toString();
    	} elseif($this->chefProjet->count()==2) {
    		$str = sprintf('%s et %s', $this->chefProjet->first()->__toString(), $this->chefProjet->last());
    	} else {
    		$str = $this->chefProjet->get(0)->__toString();
    		for($index=1;$index < $this->chefProjet->count() - 1;$index++) {
    			$str .= sprintf(', %s', $this->chefProjet->get($index)->__toString());
    		}
    		$str .= $this->chefProjet->last()->__toString();
    	}
    	return $str;
    }

    /**
     * Add domaine
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
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     */
    public function removeDomaine(\Orange\MainBundle\Entity\Domaine $domaine)
    {
        $this->domaine->removeElement($domaine);
    }

    /**
     * Get domaine
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getDomaine()
    {
        return $this->domaine;
    }
    
    /**
     * Add typeAction
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Projet
     */
    public function addTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
        $this->typeAction[] = $typeAction;
        return $this;
    }

    /**
     * Remove typeAction
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     */
    public function removeTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
        $this->typeAction->removeElement($typeAction);
    }

    /**
     * Get typeAction
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getTypeAction()
    {
        return $this->typeAction;
    }
    
    /**
     * @return boolean
     */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * @param boolean $etat
	 * @return \Orange\MainBundle\Entity\Projet
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
	}

}

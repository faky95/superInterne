<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Chantier
 * @ORM\Table(name="chantier")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ChantierRepository")
 */
class Chantier
{
	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;
	
	/**
	 * @var Projet
	 * @ORM\ManyToOne(targetEntity="Projet")
	 * @ORM\JoinColumn(name="projet_id", referencedColumnName="id", nullable=false)
	 * @Assert\NotBlank()
	 */
	private $projet;
	
	/**
	 * @var Instance
	 * @ORM\OneToOne(targetEntity="Instance", cascade={"persist", "merge", "remove"})
	 * @ORM\JoinColumn(name="instance_id", referencedColumnName="id", nullable=false)
	 */
	private $instance;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_creation", type="datetime", nullable=false)
	 */
	private $dateCreation;

	/**
	 * @var boolean
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat;
	
	/**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="chantier", cascade={"persist","remove","merge"})
     * @ORM\JoinTable(name="chef_chantier",
     *   joinColumns={
     *     @ORM\JoinColumn(name="chantier_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
	 */
	private $chefChantier;

	
	public function __construct(){
		$this->chefChantier = new  ArrayCollection();
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
     * Get libelle
     * @return string 
     */
    public function getLibelle()
    {
        return $this->instance ? $this->instance->getLibelle() : null;
    }

    /**
     * Get description
     * @return string 
     */
    public function getDescription()
    {
        return $this->instance ? $this->instance->getDescription() : null;
    }

    /**
     * Set dateCreation
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
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set etat
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
     * @return boolean 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set projet
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
     * @return \Orange\MainBundle\Entity\Projet 
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set instance
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
     * @return \Orange\MainBundle\Entity\Instance 
     */
    public function getInstance()
    {
        return $this->instance;
    }
    
    /**
     * Add chefChantier
     * @param \Orange\MainBundle\Entity\ChefChantier $chefChantier
     * @return Chantier
     */
    public function addChefChantier(\Orange\MainBundle\Entity\Utilisateur $chefChantier)
    {
    	$chefChantier->addChantier($this);
        $this->chefChantier[] = $chefChantier;
        return $this;
    }
    
    /**
     * Remove chefChantier
     * @param \Orange\MainBundle\Entity\Utilisateur $chefChantier
     */
    public function removeChefChantier(\Orange\MainBundle\Entity\Utilisateur $chefChantier)
    {
    	$this->chefChantier->removeElement($chefChantier);
    }
    
    /**
     * Get chefChantier
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChefChantier()
    {
    	return $this->chefChantier;
    }

    /**
     * list chefChantier
     * @return string
     */
     public function listChefChantier() {
    	$str = null;
    	if($this->chefChantier->count()==0) {
    		$str = 'Aucun';
    	} elseif($this->chefChantier->count()==1) {
    		$str = $this->chefChantier->first()->__toString();
    	} elseif($this->chefChantier->count()==2) {
    		$str = sprintf('%s et %s', $this->chefChantier->first()->__toString(), $this->chefChantier->last());
    	} else {
    		$str = sprintf('%s, %s ...', $this->chefChantier->first(), $this->chefChantier->get(1));
    	}
    	return $str;
    }

    /**
     * list all chefChantier
     * @return string
     */
     public function listAllChefChantier() {
    	$str = null;
    	if($this->chefChantier->count()==0) {
    		$str = 'Aucun';
    	} elseif($this->chefChantier->count()==1) {
    		$str = $this->chefChantier->first()->__toString();
    	} elseif($this->chefChantier->count()==2) {
    		$str = sprintf('%s et %s', $this->chefChantier->first()->__toString(), $this->chefChantier->last());
    	} else {
    		$str = $this->chefChantier->get(0)->__toString();
    		for($index=1;$index < $this->chefChantier->count() - 1;$index++) {
    			$str .= sprintf(', %s', $this->chefChantier->get($index)->__toString());
    		}
    		$str .= $this->chefChantier->last()->__toString();
    	}
    	return $str;
    }
    
    /**
     * get libelle
     * @return string
     */
    public function __toString() {
    	return $this->getLibelle();
    }
}

<?php
namespace Orange\MainBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Structure
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="structure")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\StructureRepository")
 */

class Structure
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
     * @var integer
     *
     * @ORM\Column(name="transverse", type="integer", nullable=true)
     */
    private $transverse;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Veuillez nommer la structure")
     * 
     */
    private $libelle;
    
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;
    
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;
    
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;
    
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
    
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Structure", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;
	
    /**
     * @var \TypeStructure
     *
     * @ORM\ManyToOne(targetEntity="TypeStructure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_structure_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Assert\NotNull(message="Veuillez choisir le type de structure")
     */
    private $typeStructure;
    
    /**
     * @var Bu
     * @ORM\ManyToOne(targetEntity="Bu", inversedBy="structureBuPrincipal")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bu_principal_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @Assert\NotNull(message="Veuillez choisir le BU principal")
     */
    private $buPrincipal;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Bu", mappedBy="structure", cascade={"persist", "merge", "remove"})
     */
    private $bu;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean", nullable=true)
     */
    private  $isDeleted;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Instance", mappedBy="structure")
     */
    private $instance;
    


    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="rapporteurStructure")
     * @ORM\JoinTable(name="rapporteur",
     *   joinColumns={
     *     @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
     */
    private $rapporteurs;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Statistique", mappedBy="structure", cascade={"persist","remove","merge"})
     */
    private $statistique;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="structure", cascade={"persist","remove","merge"})
     */
    private $utilisateurs;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Action", mappedBy="structure")
     */
    private $action;
    

    /**
     * @var ArchitectureStructure
     * @ORM\OneToOne(targetEntity="ArchitectureStructure", mappedBy="structure", cascade={"persist", "merge", "remove"})
     */
    private $architectureStructure;
    
    /**
     * Get libelle
     *
     * @return string
     */
    public function __toString()
    {
    	$object = $this;
    	$libelle = null;
    	if($object->getLvl() != 0) {
    		while($object->getLvl() > 0) {
    			$libelle = '/'.$object->getLibelle().$libelle;
    			$object = $object->getParent();
    		}
    		$libelle = $object->getLibelle().$libelle; 
    	} else {
    		$libelle .= $object->getLibelle();
    	}
    	return $libelle;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bu = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instance = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rapporteurs= new \Doctrine\Common\Collections\ArrayCollection();
        $this->utilisateurs= new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Structure
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
     * Set lft
     *
     * @param integer $lft
     * @return Structure
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return Structure
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return Structure
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }
	
    /**
     * Set root
     *
     * @param integer $root
     * @return Structure
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }
	
    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param \Orange\MainBundle\Entity\Structure $parent
     * @return Structure
     */
    public function setParent(\Orange\MainBundle\Entity\Structure $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Orange\MainBundle\Entity\Structure 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Orange\MainBundle\Entity\Structure $children
     * @return Structure
     */
    public function addChild(\Orange\MainBundle\Entity\Structure $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Orange\MainBundle\Entity\Structure $children
     */
    public function removeChild(\Orange\MainBundle\Entity\Structure $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set typeStructure
     *
     * @param \Orange\MainBundle\Entity\TypeStructure $typeStructure
     * @return Structure
     */
    public function setTypeStructure(\Orange\MainBundle\Entity\TypeStructure $typeStructure = null)
    {
        $this->typeStructure = $typeStructure;

        return $this;
    }

    /**
     * Get typeStructure
     *
     * @return \Orange\MainBundle\Entity\TypeStructure 
     */
    public function getTypeStructure()
    {
        return $this->typeStructure;
    }

    /**
     * Add bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     * @return Structure
     */
    public function addBu(\Orange\MainBundle\Entity\Bu $bu) {
    	$bu->addStructure($this);
        $this->bu->add($bu);
        return $this;
    }

    /**
     * Remove bu
     * @param \Orange\MainBundle\Entity\Bu $bu
     */
    public function removeBu(\Orange\MainBundle\Entity\Bu $bu) {
        $this->bu->removeElement($bu);
        $bu->removeStructure($this);
        return $this;
    }

    /**
     * Get bu
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBu() {
        return $this->bu;
    }
    
	public function getIsDeleted() {
		return $this->isDeleted;
	}
	public function setIsDeleted($isDeleted) {
		$this->isDeleted = $isDeleted;
		return $this;
	}
	
    
    

    /**
     * Set buPrincipal
     *
     * @param \Orange\MainBundle\Entity\Bu $buPrincipal
     * @return Structure
     */
    public function setBuPrincipal(\Orange\MainBundle\Entity\Bu $buPrincipal = null)
    {
        $this->buPrincipal = $buPrincipal;

        return $this;
    }

    /**
     * Get buPrincipal
     *
     * @return \Orange\MainBundle\Entity\Bu 
     */
    public function getBuPrincipal()
    {
        return $this->buPrincipal;
    }

    /**
     * Add instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Structure
     */
    public function addInstance(\Orange\MainBundle\Entity\Instance $instance)
    {
        $this->instance[] = $instance;

        return $this;
    }

    /**
     * Remove instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     */
    public function removeInstance(\Orange\MainBundle\Entity\Instance $instance)
    {
        $this->instance->removeElement($instance);
    }

    /**
     * Get instance
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Add rapporteurs
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $rapporteurs
     * @return Structure
     */
    public function addRapporteur(\Orange\MainBundle\Entity\Utilisateur $rapporteurs)
    {
        $this->rapporteurs[] = $rapporteurs;

        return $this;
    }

    /**
     * Remove rapporteurs
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $rapporteurs
     */
    public function removeRapporteur(\Orange\MainBundle\Entity\Utilisateur $rapporteurs)
    {
        $this->rapporteurs->removeElement($rapporteurs);
    }

    /**
     * Get rapporteurs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRapporteurs()
    {
        return $this->rapporteurs;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
    	$object = $this;
    	$libelle = null;
    	if($object->getLvl() != 0) {
    		$libelle = $object->getParent()->getName().' /'.$object->getLibelle().$libelle;
    	} else {
    		$libelle = $object->getLibelle();
    	}
    	return $libelle;
    }

    /**
     * Add statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     *
     * @return Structure
     */
    public function addStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique[] = $statistique;

        return $this;
    }

    /**
     * Remove statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     */
    public function removeStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique->removeElement($statistique);
    }

    /**
     * Get statistique
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatistique()
    {
        return $this->statistique;
    }
    
    /**
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getManager() {
    	foreach($this->utilisateurs as $utilisateur) {
    		if($utilisateur->isManager()) {
    			return $utilisateur;
    		}
    	}
    	return null;
    }

    /**
     * Add utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     *
     * @return Structure
     */
    public function addUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs[] = $utilisateur;

        return $this;
    }

    /**
     * Remove utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     */
    public function removeUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateurs->removeElement($utilisateur);
    }

    /**
     * Get utilisateur
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtilisateur()
    {
        return $this->utilisateurs;
    }
    
    public function isDeletable() {
    	return ($this->utilisateurs->count()==0 && $this->bu->count()==0 && $this->instance->count()==0);
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @param array $ids
     * @return array
     */
    public function getIdsStructureToReport($structure = null, $ids = array()) {
    	$structure = $structure ? $structure : $this;
    	foreach($structure->getChildren() as $children) {
    		$ids = $this->getIdsStructureToReport($children, $ids);
    	}
    	$ids[] = $structure->getId();
    	return $ids;
    }

	public function getArchitectureStructure() {
	return $this->architectureStructure;
}
	
	public function setArchitectureStructure(ArchitectureStructure $architectureStructure) {
	$this->architectureStructure = $architectureStructure;
	return $this;
}
	public function getTransverse() {
		return $this->transverse;
	}
	public function setTransverse($transverse) {
		$this->transverse = $transverse;
		return $this;
	}
	
	
}

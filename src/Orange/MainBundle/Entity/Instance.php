<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Orange\MainBundle\Entity\Source;

/**
 * Instance
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="instance")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\InstanceRepository")
 */
class Instance
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
     * @ORM\Column(name="libelle", type="string", length=45, nullable=false)
     * 
     * @Assert\NotBlank()
     * 
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=7, nullable=true)
     */
    private $couleur;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $description;
    
	/**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean", nullable=false)
     */
    private  $isDeleted;
    
    /**
     * @var \Chantier
     *
     * @ORM\OneToOne(targetEntity="Chantier", mappedBy="Instance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chantier_id", referencedColumnName="id")
     * })
     */
    private $chantier;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Animateur", orphanRemoval=true ,mappedBy="instance", cascade={"persist", "merge", "remove"})
     */
    private $animateur;
    
     /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Domaine", inversedBy="instance", cascade={"persist", "merge", "remove"})
     * @ORM\JoinTable(name="instance_has_domaine",
     *   joinColumns={
     *     @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
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
     * @ORM\ManyToMany(targetEntity="Structure", inversedBy="instance")
     * @ORM\JoinTable(name="instance_has_structure",
     *   joinColumns={
     *     @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $structure;
    

    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TypeAction", inversedBy="instance", cascade={"persist", "merge", "remove"})
     * @ORM\JoinTable(name="instance_has_type_action",
     *   joinColumns={
     *     @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
     *   }
     * )
     */
    private $typeAction;
    

    /**
     * @var Espace
     * @ORM\OneToOne(targetEntity="Espace", mappedBy="instance", cascade={"persist", "merge", "remove"})
     */
    private $espace;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Action", mappedBy="instance", cascade={"persist", "merge"})
     */
    private $action;
   
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Bu", mappedBy="instance")
     */
    private $bu;
    
    /**
     * @var Configuration
     * @ORM\OneToOne(targetEntity="\Orange\MainBundle\Entity\Configuration", mappedBy="instance")
     */
    private $configuration;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Source",orphanRemoval=true, mappedBy="instance", cascade={"persist", "merge","remove"})
     */
    private $sourceInstance;
    
    /**
     * 
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tmp_animateur;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $tmp_source;
    
    /**
     * @var \TypeInstance
     *
     * @ORM\ManyToOne(targetEntity="TypeInstance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_instance_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $typeInstance;
    
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", nullable=true)
     */
    private $lft;
    
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer", nullable=true)
     */
    private $lvl;
    
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", nullable=true)
     */
    private $rgt;
    
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
    
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Instance", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     *
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Instance", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     * @ORM\Column(nullable=true)
     */
    private $children;
	
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     *  @ORM\OneToMany(targetEntity="Statistique", mappedBy="type", cascade={"persist","remove","merge"})
     */
    private $statistique;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->animateur = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instance = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tmp_animateur = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isDeleted=false;
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sourceInstance = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tmp_source = new \Doctrine\Common\Collections\ArrayCollection();
        $this->structure = new \Doctrine\Common\Collections\ArrayCollection();
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bu = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Instance
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
     * Set couleur
     *
     * @param string $couleur
     * @return Instance
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string 
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Instance
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
     * Add animateur
     *
     * @param \Orange\MainBundle\Entity\Animateur $animateur
     * @return Instance
     */
    public function addAnimateur(\Orange\MainBundle\Entity\Animateur $animateur)
    {
        $this->animateur[] = $animateur;

        return $this;
    }

    /**
     * Remove animateur
     *
     * @param \Orange\MainBundle\Entity\Animateur $animateur
     */
    public function removeAnimateur(\Orange\MainBundle\Entity\Animateur $animateur)
    {
        $this->animateur->removeElement($animateur);
    }

    /**
     * Get animateur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnimateur()
    {
        return $this->animateur;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Instance
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
     * Get tmp_animateur
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpAnimateur() {
    	$this->tmp_animateur = new \Doctrine\Common\Collections\ArrayCollection();
    	foreach($this->animateur as $anim) {
    		$this->tmp_animateur->add($anim->getUtilisateur());
    	}
    	return $this->tmp_animateur;
    }
    
    /**
     * @param Utilisateur $tmp_animateur
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function addTmpAnimateur($tmp_animateur) {
    	$this->tmp_animateur->add($tmp_animateur);
    	$isExist=false;
    	foreach ($this->animateur as $anim){
    		if($anim->getUtilisateur()->getId()==$tmp_animateur->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$anim= new Animateur();
    		$anim->setInstance($this);
    		$anim->setUtilisateur($tmp_animateur);
    		$this->animateur->add($anim);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_animateur
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function removeTmpAnimateur($tmp_animateur) {
    	foreach ($this->animateur as $anim){
    		if($anim->getUtilisateur()->getId()==$tmp_animateur->getId()) {
    			$this->removeAnimateur($anim);
    			break;
    		}
    	}
    	return $this;
    }
    
    
    /**
     * Get tmp_source
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmpSource() {
    	$this->tmp_source = new \Doctrine\Common\Collections\ArrayCollection();
    	foreach($this->sourceInstance as $source) {
    		$this->tmp_source->add($source->getUtilisateur());
    	}
    	return $this->tmp_source;
    }
    
    /**
     * @param Utilisateur $tmp_source
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function addTmpSource($tmp_source) {
    	$this->tmp_source->add($tmp_source);
    	$isExist=false;
    	foreach ($this->sourceInstance as $source){
    		if($source->getUtilisateur()->getId()==$tmp_source->getId()) {
    			$isExist=true;
    			break;
    		}
    	}
    	if ($isExist==false) {
    		$source= new Source();
    		$source->setInstance($this);
    		$source->setUtilisateur($tmp_source);
    		$source->setDateAffectation(new \DateTime());
    		$this->sourceInstance->add($source);
    	}
    	return $this;
    }
    
    /**
     * @param Utilisateur $tmp_source
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function removeTmpSource($tmp_source) {
    	$idSource = null;
    	foreach ($this->sourceInstance as $source){
    		if($source->getUtilisateur()->getId()==$tmp_source->getId()) {
    			$idSource=$source;
    			break;
    		}
    	}
    	if ($idSource!==null) {
    		$this->sourceInstance->removeElement($idSource);
    	}
    	return $this;
    }

    /**
     * Set typeInstance
     *
     * @param \Orange\MainBundle\Entity\TypeInstance $typeInstance
     * @return Instance
     */
    public function setTypeInstance(\Orange\MainBundle\Entity\TypeInstance $typeInstance = null)
    {
        $this->typeInstance = $typeInstance;

        return $this;
    }

    /**
     * Get typeStructure
     *
     * @return \Orange\MainBundle\Entity\TypeInstance 
     */
    public function getTypeInstance()
    {
        return $this->typeInstance;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return Instance
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
     * @return Instance
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
     * @return Instance
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
     * @return Instance
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
     * @param \Orange\MainBundle\Entity\Instance $parent
     * @return Instance
     */
    public function setParent(\Orange\MainBundle\Entity\Instance $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Orange\MainBundle\Entity\Instance 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Orange\MainBundle\Entity\Instance $children
     * @return Instance
     */
    public function addChild(\Orange\MainBundle\Entity\Instance $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Orange\MainBundle\Entity\Instance $children
     */
    public function removeChild(\Orange\MainBundle\Entity\Instance $children)
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
     * Set children
     *
     * @param string $children
     * @return Instance
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Add sourceInstance
     *
     * @param \Orange\MainBundle\Entity\Source $sourceInstance
     * @return Instance
     */
    public function addSourceInstance(\Orange\MainBundle\Entity\Source $sourceInstance)
    {
        $this->sourceInstance[] = $sourceInstance;

        return $this;
    }

    /**
     * Remove sourceInstance
     *
     * @param \Orange\MainBundle\Entity\Source $sourceInstance
     */
    public function removeSourceInstance(\Orange\MainBundle\Entity\Source $sourceInstance)
    {
        $this->sourceInstance->removeElement($sourceInstance);
    }

    /**
     * Get sourceInstance
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSourceInstance()
    {
        return $this->sourceInstance;
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
     * Add typeAction
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
     * Add structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Instance
     */
    public function addStructure(\Orange\MainBundle\Entity\Structure $structure)
    {
        $this->structure[] = $structure;

        return $this;
    }

    /**
     * Remove structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     */
    public function removeStructure(\Orange\MainBundle\Entity\Structure $structure)
    {
        $this->structure->removeElement($structure);
    }

    /**
     * Get structure
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Add bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     * @return Instance
     */
    public function addBu(\Orange\MainBundle\Entity\Bu $bu)
    {
        $this->bu[] = $bu;

        return $this;
    }

    /**
     * Remove bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     */
    public function removeBu(\Orange\MainBundle\Entity\Bu $bu)
    {
        $this->bu->removeElement($bu);
    }

    /**
     * Get bu
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBu()
    {
        return $this->bu;
    }
    
    /**
     * get configuration
     * @return \Orange\MainBundle\Entity\Configuration
     */
    public function getConfiguration() {
    	return $this->configuration;
    }

    /**
     * Set chantier
     *
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     * @return Instance
     */
    public function setChantier(\Orange\MainBundle\Entity\Chantier $chantier = null)
    {
        $this->chantier = $chantier;

        return $this;
    }

    /**
     * Get chantier
     *
     * @return \Orange\MainBundle\Entity\Chantier 
     */
    public function getChantier()
    {
        return $this->chantier;
    }

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Instance
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

    /**
     * Add statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     *
     * @return Instance
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
    
    public function __toString()
    {
    	return $this->libelle;
    }

    /**
     * Set espace
     *
     * @param \Orange\MainBundle\Entity\Espace $espace
     *
     * @return Instance
     */
    public function setEspace(\Orange\MainBundle\Entity\Espace $espace = null)
    {
        $this->espace = $espace;

        return $this;
    }

    /**
     * Get espace
     *
     * @return \Orange\MainBundle\Entity\Espace
     */
    public function getEspace()
    {
        return $this->espace;
    }
    
    public function getIdsSourcesWithSignalisation(){
    	$ids = array();
    	$i=0;
    	foreach ($this->sourceInstance as $source){
    		if($source->getSignalisation()->count()>0){
    		    $ids[$i] = $source->getId();
    		    $i++;
    		}
    	}
    	return $ids;
    }
}

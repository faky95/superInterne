<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Processus
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="processus")
 * @ORM\Entity
 */
class Processus 
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
     * @Assert\NotNull(message="Le nom du processus est obligatoire")
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle_sans_carspecial", type="string", length=255, nullable=true)
     */
    private $libelleSansCarSpecial;
    
    /**
     * @var Structure
     *
     * @ORM\ManyToOne(targetEntity="Structure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     * })
     * @Assert\NotNull(message="Veuillez choisir la structure ...")
     */
    private $structure;
    
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
     * @ORM\ManyToOne(targetEntity="\Orange\MainBundle\Entity\Processus", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="\Orange\MainBundle\Entity\Processus", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @param string $code
	 * @return \Orange\MainBundle\Entity\Processus
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * @param string $libelle
	 * @return \Orange\MainBundle\Entity\Processus
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	/**
	 * @return Structure
	 */
	public function getStructure() {
		return $this->structure;
	}
	
	/**
	 * @param Structure $structure
	 * @return \Orange\MainBundle\Entity\Processus
	 */
	public function setStructure($structure) {
		$this->structure = $structure;
		return $this;
	}
	
	/**
	 * Get libelle
	 *
	 * @return string
	 */
	public function __toString(){
		return $this->libelle ?$this->libelle:'';
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
			$libelle = $object->getParent()->getName().' \ '.$object->getLibelle().$libelle;
		} else {
			$libelle = $object->getLibelle();
		}
		return $libelle;
	}
	


    /**
     * Set libelleSansCarSpecial
     *
     * @param string $libelleSansCarSpecial
     * @return Processus
     */
    public function setLibelleSansCarSpecial($libelleSansCarSpecial)
    {
        $this->libelleSansCarSpecial = $libelleSansCarSpecial;
    
        return $this;
    }

    /**
     * Get libelleSansCarSpecial
     *
     * @return string 
     */
    public function getLibelleSansCarSpecial()
    {
        return $this->libelleSansCarSpecial;
    }

    /**
     * Add children
     *
     * @param \Orange\MainBundle\Entity\Processus $children
     * @return Processus
     */
    public function addChild(\Orange\MainBundle\Entity\Processus $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \Orange\MainBundle\Entity\Processus $children
     */
    public function removeChild(\Orange\MainBundle\Entity\Processus $children)
    {
        $this->children->removeElement($children);
    }
    
    /**
     * @return array
     */
    public function getChildrenIds() {
    	$ids= array($this->getId());
    	foreach($this->children as $child) {
    		$ids = array_merge($ids, $child->getChildrenIds());
    	}
    	return $ids;
    }
}

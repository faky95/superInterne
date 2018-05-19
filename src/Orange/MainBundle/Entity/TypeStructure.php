<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * TypeStructure
 *
 * @ORM\Table(name="type_structure")
 * @ORM\Entity
 */
class TypeStructure
{
	
	/**
	 * @var array
	 */
	static $ids;
	
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=50, nullable=true)
     */
    private $libelle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50, nullable=true)
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=50, nullable=true)
     */
    private $niveau;
    
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
     * Set code
     *
     * @param string $code
     * @return TypeStructure
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     * @return TypeStructure
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string 
     */
    public function getNiveau()
    {
        return $this->niveau;
    }
}

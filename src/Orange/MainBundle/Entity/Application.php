<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Application
 * @ORM\Table(name="application")
 * @ORM\Entity
 */
class Application
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
	 * @ORM\Column(name="code", type="string", length=45, nullable=true)
	 * @Assert\NotNull()
	 */
	private $code;
	
	/**
	 * @var string
	 * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
	 * @Assert\NotNull()
	 */
	private $libelle;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Instance")
     * @ORM\JoinTable(name="application_has_instance",
     *   joinColumns={
     *     @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     *   }
     * )
     */
    private $instance;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instance = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString(){
    	return $this->libelle.'';
    }
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * get code
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @param string $code
	 * @return Application
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
	 * @return Application
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
    /**
     * Add instance
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Application
     */
    public function addInstance(\Orange\MainBundle\Entity\Instance $instance)
    {
        $this->instance[] = $instance;
        return $this;
    }

    /**
     * Remove instance
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
     * Get ids instance
     * @return array 
     */
    public function getInstanceIds()
    {
    	$ids = array();
    	foreach($this->instance as $instance) {
    		$ids[] = $instance->getId();
    	}
        return count($ids)!=0 ? $ids : array(-1);
    }

}

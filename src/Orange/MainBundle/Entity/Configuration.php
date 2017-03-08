<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration
 * @ORM\Table(name="configuration")
 * @ORM\Entity
 */
class Configuration
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
     * @var Config
     * @ORM\ManyToOne(targetEntity="\Orange\MainBundle\Entity\Config")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $config;
    
    /**
     * @var Instance
     * @ORM\OneToOne(targetEntity="\Orange\MainBundle\Entity\Instance")
     * @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     */
    private $instance;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat = true;
    
    /**
     * get instance
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function getInstance() {
    	return $this->instance;
    }
    
    /**
     * set instance
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return \Orange\MainBundle\Entity\Configuration
     */
    public function setInstance($instance) {
    	$this->instance = $instance;
    	return $this;
    }
    
    /**
     * get config
     * @return \Orange\MainBundle\Entity\Config
     */
    public function getConfig() {
    	return $this->config;
    }
    
    /**
     * set config
     * @param \Orange\MainBundle\Entity\Config $config
     * @return \Orange\MainBundle\Entity\Configuration
     */
    public function setConfig($config) {
    	$this->config = $config;
    	return $this;
    }
    
    /**
     * get etat
     * @return boolean
     */
	public function getEtat() {
		return $this->etat;
	}
	
	/**
	 * set etat
	 * @param boolean $etat
	 * @return \Orange\MainBundle\Entity\Configuration
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
		return $this;
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
}

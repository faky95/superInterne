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
     * @var Instance
     * @ORM\OneToOne(targetEntity="\Orange\MainBundle\Entity\Instance")
     * @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     */
    private $instance;
    
    /**
     * @var boolean
     * @ORM\Column(name="public_pas", type="boolean")
     */
    private $publicPas = false;
    
    /**
     * @var boolean
     * @ORM\Column(name="showed_all_domaines", type="boolean")
     */
    private $showedAllDomaines = false;
	
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
     * @return boolean
     */
    public function isPublicPas() {
    	return $this->publicPas;
    }
    
    /**
     * @param boolean $publicPas
     * @return \Orange\MainBundle\Entity\Configuration
     */
    public function setPublicPas($publicPas) {
    	$this->publicPas = $publicPas;
    	return $this;
    }
    
    /**
     * @return boolean
     */
    public function isShowedAllDomaines() {
    	return $this->showedAllDomaines;
    }
    
    /**
     * @param boolean $showedAllDomaines
     * @return \Orange\MainBundle\Entity\Configuration
     */
    public function setShowedAllDomaines($showedAllDomaines) {
    	$this->showedAllDomaines = $showedAllDomaines;
    	return $this;
    }
}

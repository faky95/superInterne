<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuration des BUs
 * @ORM\Table(name="bus_has_config")
 * @ORM\Entity
 */
class BuHasConfig
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
     * @var Bu
     * @ORM\ManyToOne(targetEntity="\Orange\MainBundle\Entity\Bu")
     * @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     */
    private $bu;
    
    
    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat = true;
    
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
     * Set etat
     *
     * @param boolean $etat
     *
     * @return BuHasConfig
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set config
     *
     * @param \Orange\MainBundle\Entity\Config $config
     *
     * @return BuHasConfig
     */
    public function setConfig(\Orange\MainBundle\Entity\Config $config = null)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return \Orange\MainBundle\Entity\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     *
     * @return BuHasConfig
     */
    public function setBu(\Orange\MainBundle\Entity\Bu $bu = null)
    {
        $this->bu = $bu;

        return $this;
    }

    /**
     * Get bu
     *
     * @return \Orange\MainBundle\Entity\Bu
     */
    public function getBu()
    {
        return $this->bu;
    }
}

<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domaine
 * @ORM\Table(name="domaine")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\DomaineRepository")
 */
class Domaine
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
     * @ORM\Column(name="libelle_domaine", type="string", length=45, nullable=false)
     * @Assert\NotBlank()
     * 
     */
    private $libelleDomaine;

    /**
     * @var integer
     *
     * @ORM\Column(name="active", type="integer", nullable=true)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="public", type="integer", nullable=true)
     */
    private $public;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Projet", mappedBy="domaine" )
     */
    private $projet;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Instance", mappedBy="domaine")
     */
    private $instance;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Bu", mappedBy="domaine")
     */
    private $bu;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Espace", mappedBy="domaine")
     */
    private $espace;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Action", mappedBy="domaine", cascade={"persist", "merge", "remove"})
     */
    private $action;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Signalisation", mappedBy="domaine", cascade={"persist", "merge", "remove"})
     */
    private $signalisation;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bu = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projet = new \Doctrine\Common\Collections\ArrayCollection();
        $this->action=new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function __toString(){
    	return $this->libelleDomaine;
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
     * Set libelleDomaine
     *
     * @param string $libelleDomaine
     * @return Domaine
     */
    public function setLibelleDomaine($libelleDomaine)
    {
        $this->libelleDomaine = $libelleDomaine;

        return $this;
    }

    /**
     * Get libelleDomaine
     *
     * @return string 
     */
    public function getLibelleDomaine()
    {
        return $this->libelleDomaine;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return Domaine
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set public
     *
     * @param integer $public
     * @return Domaine
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return integer 
     */
    public function getPublic()
    {
        return $this->public;
    }

    

    /**
     * Add projet
     *
     * @param \Orange\MainBundle\Entity\Projet $projet
     * @return Domaine
     */
    public function addProjet(\Orange\MainBundle\Entity\Projet $projet)
    {
        $this->projet[] = $projet;

        return $this;
    }

    /**
     * Remove projet
     *
     * @param \Orange\MainBundle\Entity\Projet $projet
     */
    public function removeProjet(\Orange\MainBundle\Entity\Projet $projet)
    {
        $this->projet->removeElement($projet);
    }

    /**
     * Get projet
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Add bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     * @return Domaine
     */
    public function addBu(\Orange\MainBundle\Entity\Bu $bu)
    {
    	$bu->addDomaine($this);
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
     * Add espace
     *
     * @param \Orange\MainBundle\Entity\Espace $espace
     * @return Domaine
     */
    public function addEspace(\Orange\MainBundle\Entity\Espace $espace)
    {
        $this->espace[] = $espace;

        return $this;
    }

    /**
     * Remove espace
     *
     * @param \Orange\MainBundle\Entity\Espace $espace
     */
    public function removeEspace(\Orange\MainBundle\Entity\Espace $espace)
    {
        $this->espace->removeElement($espace);
    }

    /**
     * Get espace
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEspace()
    {
        return $this->espace;
    }

    /**
     * Add instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Domaine
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
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return Domaine
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
     * Add signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     *
     * @return Domaine
     */
    public function addSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation[] = $signalisation;

        return $this;
    }

    /**
     * Remove signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     */
    public function removeSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation->removeElement($signalisation);
    }

    /**
     * Get signalisation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSignalisation()
    {
        return $this->signalisation;
    }
}

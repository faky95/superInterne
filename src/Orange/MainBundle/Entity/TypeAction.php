<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * TypeAction
 *
 * @ORM\Table(name="type_action")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\TypeActionRepository")
 */
class TypeAction
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
     * @ORM\Column(name="type", type="string", length=45, nullable=false)
     * @Assert\NotNull()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=8, nullable=true)
     */
    private $couleur;
    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Bu", mappedBy="typeAction")
     */
    private $bu;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Espace", mappedBy="typeAction")
     */
    private $espace;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Instance", mappedBy="typeAction")
     */
    private $instance;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Projet", mappedBy="typeAction")
     */
    private $projet;

    /**
     *
     * @ORM\OneToMany(targetEntity="Action", mappedBy="typeAction", cascade={"persist","remove","merge"})
     */
    private $action; 
    
    /**
     * 
     * @var Boolean
     */
    private $isDeleted;
    
    
    public function __construct(){
    	$this->action=new ArrayCollection();
    	$this->projet=new ArrayCollection();
    }
    
    public function __toString(){
    	return $this->type;
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
     * Set type
     *
     * @param string $type
     * @return TypeAction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     * @return TypeAction
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
     * Add bu
     *
     * @param \Orange\MainBundle\Entity\Bu $bu
     * @return TypeAction
     */
    public function addBu(\Orange\MainBundle\Entity\Bu $bu)
    {
    	$bu->addTypeAction($this);
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
     * Add instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return TypeAction
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
     * Add projet
     *
     * @param \Orange\MainBundle\Entity\Projet $projet
     * @return TypeAction
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
     * Add espace
     *
     * @param \Orange\MainBundle\Entity\Espace $espace
     *
     * @return TypeAction
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
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return TypeAction
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
}

<?php

namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tache
 *
 * @ORM\Table(name="tache")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\TacheRepository")
 */
class Tache
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
     * 
     * @ORM\ManyToOne(targetEntity="ActionCyclique", inversedBy="tache")
     * @ORM\JoinColumn(name="action_clique_id", referencedColumnName="id")
     **/
    private $actionCyclique;
    
    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="date_debut", type="datetime", nullable=true)
     */
    private $dateDebut;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cloture", type="datetime", nullable=true)
     */
    private $dateCloture;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="TacheStatut", mappedBy="tache", cascade={"persist","remove","merge"})
     */
    private $tacheStatut;
    
    /**
     * @var string
     *
     * @ORM\Column(name="etat_courant", type="string", length=255, nullable=true)
     *
     */
    private $etatCourant;
   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tacheStatut = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etatCourant = Statut::TACHE_NON_ECHUE_NON_SOLDE;
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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Tache
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateCloture
     *
     * @param \DateTime $dateCloture
     * @return Tache
     */
    public function setDateCloture($dateCloture)
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    /**
     * Get dateCloture
     *
     * @return \DateTime 
     */
    public function getDateCloture()
    {
        return $this->dateCloture;
    }

    /**
     * Set actionCyclique
     *
     * @param \Orange\MainBundle\Entity\ActionCyclique $actionCyclique
     * @return Tache
     */
    public function setActionCyclique(\Orange\MainBundle\Entity\ActionCyclique $actionCyclique = null)
    {
        $this->actionCyclique = $actionCyclique;

        return $this;
    }

    /**
     * Get actionCyclique
     *
     * @return \Orange\MainBundle\Entity\ActionCyclique 
     */
    public function getActionCyclique()
    {
        return $this->actionCyclique;
    }

    /**
     * Add tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     * @return Tache
     */
    public function addTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut[] = $tacheStatut;

        return $this;
    }

    /**
     * Remove tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     */
    public function removeTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut->removeElement($tacheStatut);
    }

    /**
     * Get tacheStatut
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTacheStatut()
    {
        return $this->tacheStatut;
    }

    /**
     * Set etatCourant
     *
     * @param string $etatCourant
     * @return Tache
     */
    public function setEtatCourant($etatCourant)
    {
        $this->etatCourant = $etatCourant;

        return $this;
    }

    /**
     * Get etatCourant
     *
     * @return string 
     */
    public function getEtatCourant()
    {
        return $this->etatCourant;
    }
}

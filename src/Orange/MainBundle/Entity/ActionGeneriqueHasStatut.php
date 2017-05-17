<?php

namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ActionGeneriqueHasStatut
 *
 * @ORM\Table(name="action_generique_has_statut")
 * @ORM\Entity
 */
class ActionGeneriqueHasStatut
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
     * @ORM\ManyToOne(targetEntity="ActionGenerique", inversedBy="actionGeneriqueHasStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $actionGenerique;
    
    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="actionGeneriqueHasStatut")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $statut;
    
    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionGeneriqueHasStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStatut", type="datetime", nullable=true)
     */
    private $dateStatut;
    
    /**
     * @var textarea
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     * 
     */
    private $commentaire;
    
    public function __construct(){
    	$this->dateStatut= new \DateTime();
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
     * Set dateOrientation
     *
     * @param \DateTime $dateOrientation
     *
     * @return ActionGeneriqueHasAction
     */
    public function setDateOrientation($dateOrientation)
    {
        $this->dateOrientation = $dateOrientation;

        return $this;
    }

    /**
     * Get dateOrientation
     *
     * @return \DateTime
     */
    public function getDateOrientation()
    {
        return $this->dateOrientation;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return ActionGeneriqueHasAction
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }


    /**
     * Set actionGenerique
     *
     * @param \Orange\MainBundle\Entity\ActionGenerique $actionGenerique
     *
     * @return ActionGeneriqueHasAction
     */
    public function setActionGenerique(\Orange\MainBundle\Entity\ActionGenerique $actionGenerique)
    {
        $this->actionGenerique = $actionGenerique;

        return $this;
    }

    /**
     * Get actionGenerique
     *
     * @return \Orange\MainBundle\Entity\ActionGenerique
     */
    public function getActionGenerique()
    {
        return $this->actionGenerique;
    }

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     *
     * @return ActionGeneriqueHasAction
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Set dateStatut
     *
     * @param \DateTime $dateStatut
     *
     * @return ActionGeneriqueHasStatut
     */
    public function setDateStatut($dateStatut)
    {
        $this->dateStatut = $dateStatut;

        return $this;
    }

    /**
     * Get dateStatut
     *
     * @return \DateTime
     */
    public function getDateStatut()
    {
        return $this->dateStatut;
    }

    /**
     * Set statut
     *
     * @param \Orange\MainBundle\Entity\Statut $statut
     *
     * @return ActionGeneriqueHasStatut
     */
    public function setStatut(\Orange\MainBundle\Entity\Statut $statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return \Orange\MainBundle\Entity\Statut
     */
    public function getStatut()
    {
        return $this->statut;
    }
}

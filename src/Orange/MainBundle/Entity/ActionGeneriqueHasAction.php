<?php

namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ActionGeneriqueHasAction
 *
 * @ORM\Table(name="action_generique_has_action")
 * @ORM\Entity
 */
class ActionGeneriqueHasAction
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
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="actionGeneriqueHasAction")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $action;
    
    /**
     * @ORM\ManyToOne(targetEntity="ActionGenerique", inversedBy="actionGeneriqueHasAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $actionGenerique;
    
    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionGeneriqueHasAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $dateOrientation;
    
    /**
     * @var textarea
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     * 
     */
    private $commentaire;
    
    public function __construct(){
    	$this->dateOrientation = new \DateTime();
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
     * Set action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     *
     * @return ActionGeneriqueHasAction
     */
    public function setAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Orange\MainBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
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
}

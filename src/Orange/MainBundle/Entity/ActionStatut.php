<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 *
 * @ORM\Table(name="action_has_statut")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionStatutRepository")
 */
class ActionStatut
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
     * @ORM\ManyToOne(targetEntity="Action", inversedBy="actionStatut")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $action;
    
    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="actionStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statut;
    
    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionStatut")
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
     * @var \Orange\MainBundle\Entity\Document
     */
    private $erq;
    
    /**
     * @var \Date
     */
    private $dateFinExecut;
    
    
    
    /**
     * @var textarea
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     * 
     */
    private $commentaire;
    
    public function __construct(){
    	$this->dateStatut = new \DateTime();
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
     * Set action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return ActionStatut
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
     * Set statut
     *
     * @param \Orange\MainBundle\Entity\Statut $statut
     * @return ActionStatut
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

    /**
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return ActionStatut
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
     * Set commentaire
     *
     * @param string $commentaire
     * @return ActionStatut
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
     * Set dateStatut
     *
     * @param \DateTime $dateStatut
     * @return ActionStatut
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
     * @return \Orange\MainBundle\Entity\Document
     */
	public function getErq() {
		return $this->erq;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Document $erq
	 * @return \Orange\MainBundle\Entity\Action
	 */
	public function setErq($erq) {
		$this->erq = $erq;
		return $this;
	}
	public function getDateFinExecut() {
		return $this->dateFinExecut;
	}
	public function setDateFinExecut($dateFinExecut) {
		$this->dateFinExecut = $dateFinExecut;
		return $this;
	}
	
	
}

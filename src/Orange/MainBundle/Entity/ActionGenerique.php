<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Orange\MainBundle\Validator\Constraints\ActionDate as ACAssert;

/**
 * Action Générique
 * @ORM\Table(name="action_generique")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\ActionGeneriqueRepository")
 * @ACAssert
 */
class ActionGenerique
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
     * @var string date
     *
     * @ORM\Column(name="reference", type="string", length=50, nullable=true)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Le libellé est obligatoire ! ")
     * 
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank(message="Vous devez donner une description pour cette action ! ")
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_action", type="datetime", nullable=true)
     */
    private $dateAction;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     * @Assert\NotBlank(message="La date de début est obligatoire ! ")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_initial", type="date", nullable=false)
     * @Assert\NotBlank(message="Le  délai est obligatoire ! ")
     */
    private $dateInitial;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="date_cloture", type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateCloture;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionGeneriqueHasStatut", mappedBy="actionGenerique", cascade={"persist", "merge", "remove"})
     */
    private $actionGeneriqueHasStatut;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionGeneriqueHasAction", mappedBy="actionGenerique", cascade={"persist", "merge", "remove"})
     */
    private $actionGeneriqueHasAction;
    
    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionGenerique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="porteur_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Choisissez le porteur de l'action ")
     */
    private $porteur;
    
    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="actionAnimateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="animateur_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $animateur;
    
    /**
     * @var string
     * @ORM\Column(name="statut", type="string", length=255, nullable=true)
     */
    private $statut;
    
    public $toDebut;
    public $fromDebut;
    public $toInitial;
    public $fromInitial;
    public $instances;
    
    
	public function __construct(){
		$this->dateAction = new \DateTime();
		$this->statut = Statut::ACTION_NON_ECHUE;
	}
    
	public function __toString(){
		return $this->libelle;
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
     * Set reference
     *
     * @param string $reference
     *
     * @return ActionGenerique
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return ActionGenerique
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ActionGenerique
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateAction
     *
     * @param \DateTime $dateAction
     *
     * @return ActionGenerique
     */
    public function setDateAction($dateAction)
    {
        $this->dateAction = $dateAction;

        return $this;
    }

    /**
     * Get dateAction
     *
     * @return \DateTime
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return ActionGenerique
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
     * Set dateInitial
     *
     * @param \DateTime $dateInitial
     *
     * @return ActionGenerique
     */
    public function setDateInitial($dateInitial)
    {
        $this->dateInitial = $dateInitial;

        return $this;
    }

    /**
     * Get dateInitial
     *
     * @return \DateTime
     */
    public function getDateInitial()
    {
        return $this->dateInitial;
    }

    /**
     * Set dateCloture
     *
     * @param \DateTime $dateCloture
     *
     * @return ActionGenerique
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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return ActionGenerique
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set isReload
     *
     * @param boolean $isReload
     *
     * @return ActionGenerique
     */
    public function setIsReload($isReload)
    {
        $this->isReload = $isReload;

        return $this;
    }

    /**
     * Get isReload
     *
     * @return boolean
     */
    public function getIsReload()
    {
        return $this->isReload;
    }

    /**
     * Set statut
     *
     * @param string $statut
     *
     * @return ActionGenerique
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Add actionStatut
     *
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     *
     * @return ActionGenerique
     */
    public function addActionStatut(\Orange\MainBundle\Entity\ActionStatut $actionStatut)
    {
        $this->actionStatut[] = $actionStatut;

        return $this;
    }

    /**
     * Remove actionStatut
     *
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     */
    public function removeActionStatut(\Orange\MainBundle\Entity\ActionStatut $actionStatut)
    {
        $this->actionStatut->removeElement($actionStatut);
    }

    /**
     * Get actionStatut
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActionStatut()
    {
        return $this->actionStatut;
    }

    /**
     * Set instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     *
     * @return ActionGenerique
     */
    public function setInstance(\Orange\MainBundle\Entity\Instance $instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return \Orange\MainBundle\Entity\Instance
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Set animateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $animateur
     *
     * @return ActionGenerique
     */
    public function setAnimateur(\Orange\MainBundle\Entity\Utilisateur $animateur = null)
    {
        $this->animateur = $animateur;

        return $this;
    }

    /**
     * Get animateur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getAnimateur()
    {
        return $this->animateur;
    }

    /**
     * Add actionGeneriqueHasStatut
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut
     *
     * @return ActionGenerique
     */
    public function addActionGeneriqueHasStatut(\Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut)
    {
        $this->actionGeneriqueHasStatut[] = $actionGeneriqueHasStatut;

        return $this;
    }

    /**
     * Remove actionGeneriqueHasStatut
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut
     */
    public function removeActionGeneriqueHasStatut(\Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut)
    {
        $this->actionGeneriqueHasStatut->removeElement($actionGeneriqueHasStatut);
    }

    /**
     * Get actionGeneriqueHasStatut
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActionGeneriqueHasStatut()
    {
        return $this->actionGeneriqueHasStatut;
    }

    /**
     * Add actionGeneriqueHasAction
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasAction $actionGeneriqueHasAction
     *
     * @return ActionGenerique
     */
    public function addActionGeneriqueHasAction(\Orange\MainBundle\Entity\ActionGeneriqueHasAction $actionGeneriqueHasAction)
    {
        $this->actionGeneriqueHasAction[] = $actionGeneriqueHasAction;

        return $this;
    }

    /**
     * Remove actionGeneriqueHasAction
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasAction $actionGeneriqueHasAction
     */
    public function removeActionGeneriqueHasAction(\Orange\MainBundle\Entity\ActionGeneriqueHasAction $actionGeneriqueHasAction)
    {
        $this->actionGeneriqueHasAction->removeElement($actionGeneriqueHasAction);
    }

    /**
     * Get actionGeneriqueHasAction
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActionGeneriqueHasAction()
    {
        return $this->actionGeneriqueHasAction;
    }

    /**
     * Set porteur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $porteur
     *
     * @return ActionGenerique
     */
    public function setPorteur(\Orange\MainBundle\Entity\Utilisateur $porteur = null)
    {
        $this->porteur = $porteur;

        return $this;
    }

    /**
     * Get porteur
     *
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getPorteur()
    {
        return $this->porteur;
    }
}

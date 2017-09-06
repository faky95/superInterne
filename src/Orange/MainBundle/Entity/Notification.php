<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="\Orange\MainBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	private $id;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="libelle", type="datetime", nullable=false)
	 */
	private $date;
	
	/**
	 * @var number
	 * @ORM\Column(name="nombre", type="smallint", nullable=false)
	 */
	private $nombre;
	
	/**
	 * @var ArrayCollection
	 * @ORM\ManyToMany(targetEntity="Utilisateur")
	 * @ORM\JoinTable(name="notification_has_destinataire",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 *   }
	 * )
	 */
	private $destinataire;
	
	/**
	 * @var TypeNotification
	 * @ORM\ManyToOne(targetEntity="TypeNotification")
	 * @ORM\JoinColumns({
	 *     @ORM\JoinColumn(name="type_notification_id", referencedColumnName="id")
	 * })
	 */
	private $typeNotification;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Utilisateur")
     * @ORM\JoinTable(name="notification_has_copy",
     *   joinColumns={
     *     @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     *   }
     * )
     */
    private $copy;
    
    /**
     * @var Structure
     */
    public $structure;
    
    /**
     * @var boolean
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat = false;
    

    public function __construct() {
    	$this->destinataire = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->copy = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @param number $nbActions
     * @param TypeNotification $typeNotification
     * @param array $destinationIds
     * @param array $copyIds
     * @param boolean $etat
     * @return \Orange\MainBundle\Entity\Notification
     */
    public static function nouvelleInstance($nbActions, $typeNotification, $destinations, $copies, $etat) {
    	$self = new self;
    	$self->setDate(new \DateTime('NOW'));
    	foreach($destinations as $destination) {
    		$self->getDestinataire()->add($destination);
    	}
    	foreach($copies as $copy) {
    		$self->getCopy()->add($copy);
    	}
    	$self->setTypeNotification($typeNotification);
    	$self->setNombre($nbActions);
    	$self->setEtat($etat);
    	return $self;
    }

    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return \Orange\MainBundle\Entity\TypeNotification
     */
	public function getTypeNotification() {
		return $this->typeNotification;
	}
	
	/**
	 * set type notification
	 * @param TypeNotification $typeNotification
	 * @return \Orange\MainBundle\Entity\Notification
	 */
	public function setTypeNotification(TypeNotification $typeNotification) {
		$this->typeNotification = $typeNotification;
		return $this;
	}
    
    /**
     * Set date
     * @param \DateTime $date
     * @return Notification
     */
    public function setDate($date)
    {
    	$this->date = $date;
    	return $this;
    }
    
    /**
     * Get date
     * @return \DateTime
     */
    public function getDate()
    {
    	return $this->date;
    }
    
    /**
     * Set nombre
     * @param number $nombre
     * @return Notification
     */
    public function setNombre($nombre)
    {
    	$this->nombre= $nombre;
    	return $this;
    }
    
    /**
     * Get nombre
     * @return number
     */
    public function getNombre()
    {
    	return $this->nombre;
    }
    
    /**
     * Get destinataire
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDestinataire()
    {
    	return $this->destinataire;
    }
    
    /**
     * Get destinataire
     * @param \Doctrine\Common\Collections\ArrayCollection $destinataire
     * @return Notification
     */
    public function setDestinataire($destinataire)
    {
    	$this->destinataire = $destinataire;
    	return $this;
    }
    
    /**
     * Get destinataire
     * @return string
     */
    public function getDestinataireInShort()
    {
    	$destinataires = null;
    	foreach($this->destinataire as $utilisateur) {
    		$destinataires = sprintf("%s ", $utilisateur);
    		break;
    	}
    	if($this->destinataire->count()==2) {
    		$destinataires.= ' et un autre destinataire';
    	}
    	if($this->destinataire->count() > 2) {
    		$destinataires.= sprintf(' et %s autres destinataires', $this->destinataire->count() - 1);
    	}
    	return $destinataires;
    }
    
    /**
     * Get copy
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCopy()
    {
    	return $this->copy;
    }
    
    /**
     * Get destinataire
     * @param \Doctrine\Common\Collections\ArrayCollection copy
     * @return Notification
     */
    public function setCopy($copy)
    {
    	$this->copy = $copy;
    	return $this;
    }
    
    /**
     * Get copy
     * @return string
     */
    public function getCopyInShort()
    {
    	$copy = null;
    	foreach($this->copy as $utilisateur) {
    		$copy= sprintf("%s ", $utilisateur);
    		break;
    	}
    	if($this->copy->count()==2) {
    		$copy.= ' et une autre personne';
    	}
    	if($this->destinataire->count() > 2) {
    		$copy.= sprintf(' et %s autres personnes', $this->copy->count() - 1);
    	}
    	return $copy;
    }
    
    /**
     * @return boolean
     */
    public function getEtat() {
    	return $this->etat;
    }
    
    /**
     * @param boolean $etat
     * @return \Orange\MainBundle\Entity\Notification
     */
    public function setEtat($etat) {
    	$this->etat = $etat;
    	return $this;
    }

}

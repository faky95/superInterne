<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Panier
 * @ORM\Table(name="panier")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\PanierRepository")
 */
class Panier
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
	 * @ORM\Column(name="date", type="datetime", nullable=true)
	 */
	private $date;
	
	/**
	 * @var Utilisateur
	 * @ORM\ManyToOne(targetEntity="\Orange\MainBundle\Entity\Utilisateur")
	 * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
	 */
	private $utilisateur;
	
	/**
	 * @var boolean
	 * @ORM\Column(name="etat", type="boolean", nullable=true)
	 */
	private $etat = true;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Action")
     * @ORM\JoinTable(name="panier_has_action",
     *   joinColumns={
     *     @ORM\JoinColumn(name="panier_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     *   }
     * )
     */
    private $action;
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->date = new \DateTime('NOW');
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * set utilisateur
	 * @param Utilisateur $utilisateur
	 * @return \Orange\MainBundle\Entity\Panier
	 */
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	
	/**
	 * @param Action $action
	 * @return \Orange\MainBundle\Entity\Panier
	 */
	public function addAction($action) {
		$p = function($key, $element) use ($action) { 
			return $action->getId() === $element->getId(); 
		};
		if(false==$this->action->exists($p)) {
			$action->addPanier($this);
			$this->action->add($action);
		}
		return $this;
	}
	
}

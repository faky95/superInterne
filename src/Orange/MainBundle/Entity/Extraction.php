<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extraction
 * @ORM\Table(name="extraction")
 * @ORM\Entity
 */
class Extraction
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
     * @ORM\Column(name="date_action", type="datetime", nullable=true)
     */
    private $dateAction;

    /**
     * @var Utilisateur
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
     * })
     */
    private $utilisateur;
    
    /**
     * @var string
     * @ORM\Column(name="query", type="text", nullable=true)
     */
    private $query;
    
    /**
     * @var string
     * @ORM\Column(name="param", type="text", nullable=true)
     */
    private $param;
    
    /**
     * @var integer
     * @ORM\Column(name="etat", type="integer", length=1, nullable=true)
     */
    private $etat = 0;
    
    
    public function __construct() {
    	$this->dateAction = new \DateTime('NOW');
    }
    
    
   /**
    * @param number $totalNumber
    * @param Utilisateur $utilisateur
    * @param string $query
    * @param \Doctrine\Common\Collections\ArrayCollection $param
    */
    public static function nouvelleTache($totalNumber, $utilisateur, $query, $param) {
    	$entity = new self;
    	$entity->utilisateur = $utilisateur;
    	$entity->dateAction = new \DateTime('NOW');
    	$entity->query = $query;
    	$entity->param = $param;
    	$entity->etat = 0;
    	return $entity;
    	
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
     * Get dateAction
     *
     * @return \DateTime 
     */
    public function getDateAction()
    {
        return $this->dateAction;
    }

    /**
     * Get utilisateur
     * @return \Orange\MainBundle\Entity\Utilisateur 
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
    
    /**
     * @return string
     */
    public function getQuery() {
    	return $this->query;
    }
    
    /**
     * @return string
     */
    public function getParam() {
    	return $this->param;
    }
    
    /**
     * @param integer $etat
     * @return \Orange\MainBundle\Entity\Extraction
     */
    public function setEtat($etat) {
    	$this->etat = $etat;
    	return $this;
    }
    
    /**
     * @return number
     */
    public function getEtat() {
    	return $this->etat;
    }

}

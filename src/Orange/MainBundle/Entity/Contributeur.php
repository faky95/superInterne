<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contributeur d'une action
 *
 * @ORM\Table(name="contributeur")
 * @ORM\Entity
 */
class Contributeur
{
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
     private  $id;
     
     /**
      * @var \Utilisateur
      *
      * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="contributeurs")
      * @ORM\JoinColumns({
      *   @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id")
      * })
      */
      private $utilisateur;
      
     /**
      * @ORM\ManyToOne(targetEntity="Action", inversedBy="contributeur")
      * @ORM\JoinColumn(nullable=false)
      */
      private $action;


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
     * Set utilisateur
     *
     * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
     * @return Contributeur
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur = null)
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
     * Set action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Contributeur
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
    
    public function __toString()
    {
    	return $this->getUtilisateur()." ";
    }
}

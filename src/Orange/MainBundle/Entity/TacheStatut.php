<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 *
 * @ORM\Table(name="tache_has_statut")
 * @ORM\Entity()
 */
class TacheStatut
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
     * @ORM\ManyToOne(targetEntity="Tache", inversedBy="tacheStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tache;
    
    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="tacheStatut")
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
     * @var textarea
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
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
     * Set dateStatut
     *
     * @param \DateTime $dateStatut
     * @return TacheStatut
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
     * Set commentaire
     *
     * @param string $commentaire
     * @return TacheStatut
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
     * Set tache
     *
     * @param \Orange\MainBundle\Entity\Tache $tache
     * @return TacheStatut
     */
    public function setTache(\Orange\MainBundle\Entity\Tache $tache)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return \Orange\MainBundle\Entity\Tache 
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set statut
     *
     * @param \Orange\MainBundle\Entity\Statut $statut
     * @return TacheStatut
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
     * @return TacheStatut
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

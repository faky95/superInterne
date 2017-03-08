<?php


namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Statut
 *
 * @ORM\Table(name="signalisation_has_statut")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\SignalisationStatutRepository")
 */
class SignalisationStatut
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
     * @ORM\ManyToOne(targetEntity="Signalisation", inversedBy="signStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $signalisation;
    
    /**
     * @ORM\ManyToOne(targetEntity="Statut", inversedBy="signStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statut;
    
    
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
     * @Assert\NotNull(message="Le motif est obligatoire")
     */
    private $commentaire;
    
    /**
     * @var \Boolean
     *
     * @ORM\Column(name="en_cours", type="boolean", nullable=true)
     */
    private $enCours;
    
    /**
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="signStatut")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
    
    public function __construct(){
    	$this->setDateStatut(new  \DateTime('now'));
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
     * @return SignalisationStatut
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
     * Set signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     * @return SignalisationStatut
     */
    public function setSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation = $signalisation;

        return $this;
    }

    /**
     * Get signalisation
     *
     * @return \Orange\MainBundle\Entity\Signalisation 
     */
    public function getSignalisation()
    {
        return $this->signalisation;
    }

    /**
     * Set statut
     *
     * @param \Orange\MainBundle\Entity\Statut $statut
     * @return SignalisationStatut
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
     * @return SignalisationStatut
     */
    public function setUtilisateur(\Orange\MainBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
    
    public function __toString()
    {
    	return $this->statut." ";
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
     * @return SignalisationStatut
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
     * Set enCours
     *
     * @param boolean $enCours
     *
     * @return SignalisationStatut
     */
    public function setEnCours($enCours)
    {
        $this->enCours = $enCours;

        return $this;
    }

    /**
     * Get enCours
     *
     * @return boolean
     */
    public function getEnCours()
    {
        return $this->enCours;
    }
}

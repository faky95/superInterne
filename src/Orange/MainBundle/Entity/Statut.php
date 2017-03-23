<?php
namespace Orange\MainBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 *
 * @ORM\Table(name="statut")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\StatutRepository")
 */
class Statut
{
	// Constantes relatives au changement de statut d'une signalisation	
	const NOUVELLE_SIGNALISATION 					= 'SIGN_NOUVELLE';
	const SIGNALISATION_PRISE_CHARGE 				= 'SIGN_PRISE_EN_CHARGE';
	const SIGNALISATION_VALIDER 					= 'SIGN_VALIDE';
	const SIGNALISATION_INVALIDER 					= 'SIGN_INVALIDE';
	const SIGNALISATION_ABANDONNER 					= 'SIGN_ABANDON';
	const SIGNALISATION_RECHARGER 					= 'SIGN_RECHARGE';
	const TRAITEMENT_SIGNALISATION 					= 'SIGN_TRAITEMENT';
	const FIN_TRAITEMENT_SIGNALISATION 				= 'SIGN_TRAITEMENT_FIN';
	const SIGNALISATION_TRAITE_EFFICACEMENT 		= 'SIGN_TRAIT_EFFICACE';
	const SIGNALISATION_TRAITE_NON_EFFICACEMENT 	= 'SIGN_TRAIT_NON_EFFICACE';
	const SIGNALISATION_CLOTURE 					= 'SIGN_CLOTURE';
	
	// Constantes relatives au changement de statut d'une action
	const ACTION_VALIDER 							= 'ACTION_VALIDEE';
	const ACTION_INVALIDER							= 'ACTION_INVALIDEE';
	const ACTION_REPORT						 	    = 'ACTION_REPORT';
	const ACTION_RECHARGER						 	= 'ACTION_RECHARGER';
	
	const EVENEMENT_VALIDER 		        		= 'EVENEMENT_VALIDER';
	const EVENEMENT_INVALIDER 		        		= 'EVENEMENT_INVALIDER';
	
	const EVENEMENT_REAFFECTE 		        		= 'EVENEMENT_REAFFECTE';
	const EVENEMENT_ARCHIVE                 		= 'EVENEMENT_ARCHIVE';
	const EVENEMENT_CLOTURE 		            	= 'EVENEMENT_CLOTURE';
	const EVENEMENT_DEMANDE_ABANDON_ACCEPTE	        = 'EVENEMENT_DEMANDE_ABANDON_ACCEPTE';
	const EVENEMENT_DEMANDE_ABANDON_REFUS		    = 'EVENEMENT_DEMANDE_ABANDON_REFUS';
	const EVENEMENT_DEMANDE_DE_REPORT		        = 'EVENEMENT_DEMANDE_DE_REPORT';
	const EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE	    = 'EVENEMENT_DEMANDE_DE_REPORT_ACCEPTE';
	const EVENEMENT_DEMANDE_DE_REPORT_REFUS	    	= 'EVENEMENT_DEMANDE_DE_REPORT_REFUS';
	const EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE    = 'EVENEMENT_VALIDATION_ANIMATEUR_ATTENTE';
	const EVENEMENT_VALIDATION_MANAGER_ATTENTE   	= 'EVENEMENT_VALIDATION_MANAGER_ATTENTE';
	const EVENEMENT_DEMANDE_ABANDON				 	= 'EVENEMENT_DEMANDE_ABANDON';
	const EVENEMENT_DEMANDE_REPORT				 	= 'EVENEMENT_DEMANDE_REPORT';
	const EVENEMENT_DEMANDE_SOLDE				 	= 'EVENEMENT_DEMANDE_SOLDE';
    const EVENEMENT_PAS_SOLDER                      = 'EVENEMENT_PAS_SOLDER';

	const ACTION_NOUVELLE 							= 'ACTION_NOUVELLE';
	const ACTION_DEMANDE_ABANDON					= 'ACTION_DEMANDE_ABANDON';
	const ACTION_DEMANDE_REPORT				    	= 'ACTION_DEMANDE_REPORT';
	const ACTION_ABANDONNEE 						= 'ACTION_ABANDONNEE';
	const ACTION_REPORTEE 							= 'ACTION_REPORTEE';
	const ACTION_ECHUE_NON_SOLDEE 					= 'ACTION_ECHUE_NON_SOLDEE';
	const ACTION_NON_ECHUE 							= 'ACTION_NON_ECHUE';
	const ACTION_SOLDEE_DELAI 						= 'ACTION_SOLDEE_DELAI';
	const ACTION_SOLDEE_HORS_DELAI 					= 'ACTION_SOLDEE_HORS_DELAI';
	const ACTION_FAIT_DELAI							= 'ACTION_FAIT_DELAI';
	const ACTION_FAIT_HORS_DELAI					= 'ACTION_FAIT_HORS_DELAI';
	const ACTION_NON_SOLDEE							= 'ACTION_NON_SOLDEE';
	
	const ACTION_SOLDEE								= 'ACTION_SOLDEE';
	const ACTION_EN_COURS							= 'ACTION_EN_COURS';
	
	const SOLDEE_ARCHIVEE							= 'SOLDEE_ARCHIVEE';
	const ABANDONNEE_ARCHIVEE					    = 'ABANDONNEE_ARCHIVEE';
	
	/**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=true)
     */
    private $libelle;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="ActionStatut", mappedBy="statut", cascade={"persist","remove","merge"})
     */
    private $actionStatut;
    
    
    /**
     *
     * @ORM\OneToMany(targetEntity="TacheStatut", mappedBy="statut", cascade={"persist","remove","merge"})
     */
    private $tacheStatut;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="SignalisationStatut", mappedBy="statut", cascade={"persist","remove","merge"})
     */
    private $statutSign;
    
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=45, nullable=true)
     */
    private $code;
    
    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=45, nullable=true)
     */
    private $couleur;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="display", type="integer", nullable=true)
     */
    private $display;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="change", type="integer", nullable=true)
     */
    private $change;
    
    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=45, nullable=true)
     */
    private $statut;
    
    /**
     * @var \TypeStatut
     *
     * @ORM\ManyToOne(targetEntity="TypeStatut", cascade={"persist","remove","merge"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_statut_id", referencedColumnName="id")
     * })
     */
    private $typeStatut;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionGeneriqueHasStatut", mappedBy="statut", cascade={"persist", "merge", "remove"})
     */
    private $actionGeneriqueHasStatut;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_generique", type="boolean",  nullable=true)
     */
    private $isGenerique;
    
    public function __toString(){
    	return $this->libelle;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->actionStatut = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isGenerique = false;
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
     * Set libelle
     *
     * @param string $libelle
     * @return Statut
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
     * Set code
     * @param string $code
     * @return Statut
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set couleur
     * @param string $couleur
     * @return Statut
     */
    public function setCouleur($couleur) {
        $this->couleur = $couleur;
        return $this;
    }

    /**
     * Get couleur
     * @return string 
     */
    public function getCouleur() {
        return $this->couleur;
    }

    /**
     * Add actionStatut
     *
     * @param \Orange\MainBundle\Entity\ActionStatut $actionStatut
     * @return Statut
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
     * Add statutSign
     *
     * @param \Orange\MainBundle\Entity\SignalisationStatut $statutSign
     * @return Statut
     */
    public function addStatutSign(\Orange\MainBundle\Entity\SignalisationStatut $statutSign)
    {
        $this->statutSign[] = $statutSign;

        return $this;
    }

    /**
     * Remove statutSign
     *
     * @param \Orange\MainBundle\Entity\SignalisationStatut $statutSign
     */
    public function removeStatutSign(\Orange\MainBundle\Entity\SignalisationStatut $statutSign)
    {
        $this->statutSign->removeElement($statutSign);
    }

    /**
     * Get statutSign
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatutSign()
    {
        return $this->statutSign;
    }

    /**
     * Set typeStatut
     *
     * @param \Orange\MainBundle\Entity\TypeStatut $typeStatut
     * @return Statut
     */
    public function setTypeStatut(\Orange\MainBundle\Entity\TypeStatut $typeStatut )
    {
        $this->typeStatut = $typeStatut;

        return $this;
    }

    /**
     * Get typeStatut
     *
     * @return \Orange\MainBundle\Entity\TypeStatut 
     */
    public function getTypeStatut()
    {
        return $this->typeStatut;
    }

    /**
     * Add tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     * @return Statut
     */
    public function addTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut[] = $tacheStatut;

        return $this;
    }

    /**
     * Remove tacheStatut
     *
     * @param \Orange\MainBundle\Entity\TacheStatut $tacheStatut
     */
    public function removeTacheStatut(\Orange\MainBundle\Entity\TacheStatut $tacheStatut)
    {
        $this->tacheStatut->removeElement($tacheStatut);
    }

    /**
     * Get tacheStatut
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTacheStatut()
    {
        return $this->tacheStatut;
    }
    
	public function getDisplay() {
		return $this->display;
	}
	
	public function setDisplay($display) {
		$this->display = $display;
		return $this;
	}
	public function getChange() {
		return $this->change;
	}
	public function setChange($change) {
		$this->change = $change;
		return $this;
	}
	public function getStatut() {
		return $this->statut;
	}
	public function setStatut($statut) {
		$this->statut = $statut;
		return $this;
	}
	
	
	

    /**
     * Add actionGeneriqueHasStatut
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut
     *
     * @return Statut
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
     * Set isGenerique
     *
     * @param boolean $isGenerique
     *
     * @return Statut
     */
    public function setIsGenerique($isGenerique)
    {
        $this->isGenerique = $isGenerique;

        return $this;
    }

    /**
     * Get isGenerique
     *
     * @return boolean
     */
    public function getIsGenerique()
    {
        return $this->isGenerique;
    }
}

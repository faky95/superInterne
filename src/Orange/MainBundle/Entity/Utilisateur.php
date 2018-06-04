<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\UtilisateurRepository")
 * @UniqueEntity("matricule")
 */
class Utilisateur extends BaseUser
{
	
	const ROLE_SUPER_ADMIN						= 'ROLE_SUPER_ADMIN';
	const ROLE_ADMIN							= 'ROLE_ADMIN';
	const ROLE_MANAGER 							= 'ROLE_MANAGER';
	const ROLE_ANIMATEUR 						= 'ROLE_ANIMATEUR';
	const ROLE_CHEF_CHANTIER					= 'ROLE_CHEF_CHANTIER';
	const ROLE_CHEF_PROJET						= 'ROLE_CHEF_PROJET';
	const ROLE_GESTIONNAIRE_ESPACE				= 'ROLE_GESTIONNAIRE_ESPACE';
	const ROLE_MEMBRE_ESPACE				    = 'ROLE_MEMBRE_ESPACE';
	const ROLE_PORTEUR 							= 'ROLE_PORTEUR';
	const ROLE_SOURCE 							= 'ROLE_SOURCE';
	const ROLE_PILOTE 							= 'ROLE_PILOTE';
	const ROLE_RAPPORTEUR 						= 'ROLE_RAPPORTEUR';
	const ROLE_CONTRIBUTEUR						= 'ROLE_CONTRIBUTEUR';
	const ROLE_ANIMATEUR_ONLY					= 'ROLE_ANIMATEUR_ONLY';
	const ROLE_ANIMATEUR_ACTIONGENERIQUE		= 'ROLE_ANIMATEUR_ACTIONGENERIQUE';
	
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
	
    /**
     * @var string
     * @ORM\Column(name="prenom", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Vous devez renseigner le prénom de l'utilisateur ! ")
     */
    protected $prenom;

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Vous devez renseigner le nom de l'utilisateur ! ")
     */
    protected $nom;

    /**
     * @var string
     * @ORM\Column(name="matricule", type="string", length=100, nullable=true)
     */
    protected $matricule;

    /**
     * @var string
     * @ORM\Column(name="telephone", type="string", length=25, nullable=true)
     * @Assert\NotBlank(message="Vous devez renseigner le téléphone de l'utilisateur ! ")
     */
    protected $telephone;

    /**
     * @var boolean
     * @ORM\Column(name="manager", type="boolean", nullable=true)
     */
    protected $manager;
    
    /**
     * @var boolean
     * @ORM\Column(name="admin", type="boolean", nullable=true)
     */
    protected $isAdmin;

    /**
     * @var Structure
     * @ORM\ManyToOne(targetEntity="Structure")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank(message="Vous devez renseigner la structure de l'utilisateur ! ")
     */
    private $structure;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Instance", mappedBy="instance")
     */
    private $instance;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Groupe", mappedBy="utilisateur")
     */
    private $groupe;
    
    /**
     * @ORM\OneToMany(targetEntity="Animateur", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $animators;
    
    /**
     * @ORM\OneToMany(targetEntity="Contributeur", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $contributeurs;
    
    /**
     * @ORM\OneToMany(targetEntity="Source", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $sources;
    
    /**
     * @ORM\ManyToMany(targetEntity="Projet", mappedBy="chefProjet", cascade={"persist","remove","merge"})
     */
    private $projet;
    
    /**
     * @ORM\ManyToMany(targetEntity="Chantier", mappedBy="chefChantier", cascade={"persist","remove","merge"})
     */
    private $chantier;
    
    /**
     * @ORM\OneToMany(targetEntity="MembreEspace", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $membreEspace;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *  @ORM\OneToMany(targetEntity="Signalisation", mappedBy="constatateur", cascade={"persist","remove","merge"})
     */
    private $signalisation;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Structure", mappedBy="rapporteurs")
     */
    private $rapporteurStructure;
    
    /**
     * @ORM\OneToMany(targetEntity="SignalisationAnimateur", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $signalisationAnimateur;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Reporting", mappedBy="destinataire")
     */
    private $reporting;
	
	/**
     * @var boolean
     * @ORM\Column(name="first_change_password", type="boolean", nullable=true)
     */
    protected $firstChangePassword;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionGeneriqueHasStatut", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $actionGeneriqueHasStatut;
    
    /**
     * @ORM\OneToMany(targetEntity="ActionGeneriqueHasAction", mappedBy="utilisateur", cascade={"persist","remove","merge"})
     */
    private $actionGeneriqueHasAction;
    
    /**
     * @var boolean
     * @ORM\Column(name="can_create_actiongenerique", type="boolean", nullable=true)
     */
    private $canCreateActionGenerique;
    
    /**
     * get full name
     */
    public function __toString() {
    	return $this->prenom.' '.strtoupper($this->nom).' [ '.$this->getDirection().' ]';
    }
	
    /**
     * @param number $id
     * @return Utilisateur
     */
	public static function newWithId($id) {
		$self = new self;
		$self->id = $id;
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
     * Set prenom
     * @param string $prenom
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    /**
     * Get prenom
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return strtoupper($this->nom);
    }

    /**
     * Get nom complet
     * @return string 
     */
    public function getNomComplet()
    {
        return $this->prenom.' '.strtoupper($this->nom).' [ '.$this->getDirection().' ]';
    }

    /**
     * Set matricule
     *
     * @param string $matricule
     * @return Utilisateur
     */
    public function setMatricule($matricule)
    {
        $this->matricule = $matricule;

        return $this;
    }

    /**
     * Get matricule
     *
     * @return string 
     */
    public function getMatricule()
    {
        return $this->matricule;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Utilisateur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set manager
     *
     * @param boolean $manager
     * @return Utilisateur
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return boolean 
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCollaborator() {
    	$collaborators = new \Doctrine\Common\Collections\ArrayCollection();
    	if(!$this->manager) {
    		return $collaborators;
    	}
    	foreach($this->structure->getUtilisateur() as $utilisateur) {
    		if($utilisateur->getId()!=$this->getId()) {
    			$collaborators->add($utilisateur);
    		}
    	}
    	foreach($this->structure->getChildren() as $structure) {
    		/*foreach ($structure->getUtilisateur() as $utilisateur){
    			$collaborators->add($utilisateur);
    		}*/
    		$manager = $structure->getManager();
    		if($manager) {
    			$collaborators->add($manager);
    		}
    	}
    	return $collaborators;
    }
    
    /**
     * @param Structure $structure
     * @return array
     */
    public function getAllInstances($structure){
    	$ids = array(0);
    	foreach($structure->getInstance() as $value) {
    		$ids[] = $value->getId();
    	}
    	return $ids;
    }
    
    /**
     * @return array
     */
    public function getCollaboratorsId() {
    	$ids = array(0);
    	foreach($this->getCollaborator() as $utilisateur) {
    		$ids[] = $utilisateur->getId();
    	}
    	return $ids;
    }

    /**
     * Set isAdmin
     * @param boolean $isAdmin
     * @return Utilisateur
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    /**
     * Get isAdmin
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set structure
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Utilisateur
     */
    public function setStructure(\Orange\MainBundle\Entity\Structure $structure = null)
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * Get structure
     *
     * @return \Orange\MainBundle\Entity\Structure 
     */
    public function getStructure()
    {
        return $this->structure;
    }
    
    public function getProfil() {
    	$profil = array('Porteur');
    	if($this->getAnimators()->count()>0) {
    		array_push($profil, "Animateur");
    	} elseif($this->manager) {
    		array_push($profil, "Manager");
    	} elseif( $this->getContributeurs()->count()>0) {
    		array_push($profil, "Contributeur");
    	} elseif($this->getRapporteurStructure()->count()>0) {
    		array_push($profil, "Rapporteur");
    	} elseif($this->isAdmin) {
    		array_push($profil, "Administrateur");
    	} elseif($this->getSources()->count()>0) {
    		array_push($profil, "Source");
    	} elseif($this->getProjet()->count()>0) {
    		array_push($profil, "Chef de projet");
    	} elseif($this->getChantier()->count()>0) {
    		array_push($profil, "Chef de chantier");
    	}
    	return $profil;
    }
    /**
     * (non-PHPdoc)
     * @see \FOS\UserBundle\Model\User::hasRole()
     */
    public function hasRole($role) {
    	if(strtoupper($role)==self::ROLE_SUPER_ADMIN) {
    		return parent::hasRole(self::ROLE_SUPER_ADMIN);
    	} elseif(strtoupper($role)==self::ROLE_ADMIN) {
    		return $this->isAdmin;
    	} elseif(strtoupper($role)==self::ROLE_ANIMATEUR_ONLY) {
    		return $this->getAnimators(true)->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_ANIMATEUR) {
    		return $this->getAnimators()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_PORTEUR) {
    		return true;
    	} elseif(strtoupper($role)==self::ROLE_MANAGER) {
    		return $this->manager;
    	} elseif(strtoupper($role)==self::ROLE_CONTRIBUTEUR) {
    		return true;
    	}elseif(strtoupper($role)==self::ROLE_SOURCE) {
    		return $this->getSources()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_CHEF_PROJET) {
    		return $this->getProjet()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_GESTIONNAIRE_ESPACE) {
    		$trouve=false;
    		if($this->getMembreEspace()->count()>0)
	    		foreach ($this->membreEspace as $member)
	    			if($member->getIsGestionnaire()) {
	    				$trouve=true;
	    				break;
	    			}
	    		return $trouve;
    	}  elseif(strtoupper($role)==self::ROLE_MEMBRE_ESPACE) {
    		return $this->getMembreEspace()->count()>0;
    	}elseif(strtoupper($role)==self::ROLE_PILOTE) {
    		$res = false;
    		foreach ($this->getAnimators() as $animateur)
    			if($animateur->getInstance()->getTypeInstance()->getId() == 2) {
    				$res=true;
    				break;
    			}
	    	return $res;
    	} elseif(strtoupper($role)==self::ROLE_RAPPORTEUR) {
    		return $this->getRapporteurStructure()->count()>0;
    	} elseif(strtoupper($role)==self::ROLE_ANIMATEUR_ACTIONGENERIQUE) {
    		return $this->canCreateActionGenerique;
    	} else {
    		
    	}
    	return false;
    }
    
    /**
     * @param array $roles
     * @return boolean
     */
    public function hasRoles($roles) {
    	foreach($roles as $role) {
    		if($this->hasRole($role)) {
    			return true;
    		}
    	}
    	return false;
    }
        
    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
        $this->instance = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->animators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sources = new \Doctrine\Common\Collections\ArrayCollection();
        $this->projet = new \Doctrine\Common\Collections\ArrayCollection();
        $this->chantier = new \Doctrine\Common\Collections\ArrayCollection();
        $this->action = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reporting = new \Doctrine\Common\Collections\ArrayCollection();
        $this->firstChangePassword = 1;
        $this->rapporteurStructure= new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Utilisateur
     */
    public function addInstance(\Orange\MainBundle\Entity\Instance $instance)
    {
        $this->instance[] = $instance;
        return $this;
    }

    /**
     * Remove instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     */
    public function removeInstance(\Orange\MainBundle\Entity\Instance $instance)
    {
        $this->instance->removeElement($instance);
    }

    /**
     * Get instance
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Add groupe
     * @param \Orange\MainBundle\Entity\Groupe $groupe
     * @return Utilisateur
     */
    public function addGroupe(\Orange\MainBundle\Entity\Groupe $groupe)
    {
        $this->groupe[] = $groupe;

        return $this;
    }

    /**
     * Remove groupe
     *
     * @param \Orange\MainBundle\Entity\Groupe $groupe
     */
    public function removeGroupe(\Orange\MainBundle\Entity\Groupe $groupe)
    {
        $this->groupe->removeElement($groupe);
    }

    /**
     * Get groupe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Add animators
     * @param \Orange\MainBundle\Entity\Animateur $animators
     * @return Utilisateur
     */
    public function addAnimator(\Orange\MainBundle\Entity\Animateur $animators)
    {
        $this->animators[] = $animators;

        return $this;
    }

    /**
     * Remove animators
     *
     * @param \Orange\MainBundle\Entity\Animateur $animators
     */
    public function removeAnimator(\Orange\MainBundle\Entity\Animateur $animators)
    {
        $this->animators->removeElement($animators);
    }

    /**
     * Get animators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnimators($strict = false)
    {
        return $this->animators->filter(function ($animateur) use($strict){
        	return ($animateur->getInstance()->getEspace()==null && $animateur->getInstance()->getChantier()==null) || $strict == false; 
        });
    }

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Utilisateur
     */
    public function addActionPortee(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action[] = $action;

        return $this;
    }

    /**
     * Add sources
     *
     * @param \Orange\MainBundle\Entity\Source $sources
     * @return Utilisateur
     */
    public function addSource(\Orange\MainBundle\Entity\Source $sources)
    {
        $this->sources[] = $sources;

        return $this;
    }

    /**
     * Remove sources
     *
     * @param \Orange\MainBundle\Entity\Source $sources
     */
    public function removeSource(\Orange\MainBundle\Entity\Source $sources)
    {
        $this->sources->removeElement($sources);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Add action
     *
     * @param \Orange\MainBundle\Entity\Action $action
     * @return Utilisateur
     */
    public function addAction(\Orange\MainBundle\Entity\Action $action)
    {
        $this->action[] = $action;

        return $this;
    }

    /**
     * Get projet
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Add projet
     * @param \Orange\MainBundle\Entity\Projet $projet
     * @return Utilisateur
     */
    public function addProjet(\Orange\MainBundle\Entity\Projet $projet)
    {
        $this->projet[] = $projet;
        return $this;
    }

    /**
     * Remove projet
     * @param \Orange\MainBundle\Entity\Projet $projet
     */
    public function removeProjet(\Orange\MainBundle\Entity\Projet $projet)
    {
        $this->projet->removeElement($projet);
    }

    /**
     * Get chantiers
     * @return \Doctrine\Common\Collections\ArrayCollection 
     */
    public function getChantier()
    {
        return $this->chantier;
    }

    /**
     * Add chantier
     * @param \Orange\MainBundle\Entity\Chantier $chantier
     * @return Utilisateur
     */
    public function addChantier(\Orange\MainBundle\Entity\Chantier $chantier)
    {
        $this->chantier[] = $chantier;
        return $this;
    }

    /**
     * Remove chantiers
     * @param \Orange\MainBundle\Entity\Projet $chantiers
     */
    public function removeChantier(\Orange\MainBundle\Entity\Chantier $chantier)
    {
        $this->chantier->removeElement($chantier);
    }

    /**
     * Add membreEspace
     * @param \Orange\MainBundle\Entity\MembreEspace $membreEspace
     * @return Utilisateur
     */
    public function addMembreEspace(\Orange\MainBundle\Entity\MembreEspace $membreEspace)
    {
        $this->membreEspace[] = $membreEspace;
        return $this;
    }

    /**
     * Remove membreEspace
     * @param \Orange\MainBundle\Entity\MembreEspace $membreEspace
     */
    public function removeMembreEspace(\Orange\MainBundle\Entity\MembreEspace $membreEspace)
    {
        $this->membreEspace->removeElement($membreEspace);
    }

    /**
     * Get membreEspace
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembreEspace()
    {
        return $this->membreEspace;
    }

    /**
     * Add rapporteurStructure
     * @param \Orange\MainBundle\Entity\Structure $rapporteurStructure
     * @return Utilisateur
     */
    public function addRapporteurStructure(\Orange\MainBundle\Entity\Structure $rapporteurStructure)
    {
        $this->rapporteurStructure[] = $rapporteurStructure;
        return $this;
    }

    /**
     * Remove rapporteurStructure
     * @param \Orange\MainBundle\Entity\Structure $rapporteurStructure
     */
    public function removeRapporteurStructure(\Orange\MainBundle\Entity\Structure $rapporteurStructure)
    {
        $this->rapporteurStructure->removeElement($rapporteurStructure);
    }

    /**
     * Retourne les structures du rapporteur sous forme de tableau(id, libelle)
     *
     */
    public function getArrayRapporteurStructure()
    {
    	$arrStructs=array();
    	foreach($this->rapporteurStructure as $structure){
    		$arrStructs[] = array('id'=>$structure->getId(), 'libelle'=>$structure->getLibelle());
    	}
    	return $arrStructs;
    }
    
    /**
     * Get rapporteurStructure
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRapporteurStructure()
    {
        return $this->rapporteurStructure;
    }
    
	/**
     * Set firstChangePassword
     *
     * @param boolean $firstChangePassword
     *
     * @return Utilisateur
     */
    public function setFirstChangePassword($firstChangePassword)
    {
        $this->firstChangePassword = $firstChangePassword;

        return $this;
    }

    /**
     * Get firstChangePassword
     *
     * @return boolean
     */
    public function getFirstChangePassword()
    {
        return $this->firstChangePassword;
    }

    /**
     * Add signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     * @return Utilisateur
     */
    public function addSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation[] = $signalisation;

        return $this;
    }

    /**
     * Remove signalisation
     *
     * @param \Orange\MainBundle\Entity\Signalisation $signalisation
     */
    public function removeSignalisation(\Orange\MainBundle\Entity\Signalisation $signalisation)
    {
        $this->signalisation->removeElement($signalisation);
    }

    /**
     * Get signalisation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignalisation()
    {
        return $this->signalisation;
    }

    /**
     * Add signalisationAnimateur
     *
     * @param \Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur
     * @return Utilisateur
     */
    public function addSignalisationAnimateur(\Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur)
    {
        $this->signalisationAnimateur[] = $signalisationAnimateur;

        return $this;
    }

    /**
     * Remove signalisationAnimateur
     *
     * @param \Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur
     */
    public function removeSignalisationAnimateur(\Orange\MainBundle\Entity\SignalisationAnimateur $signalisationAnimateur)
    {
        $this->signalisationAnimateur->removeElement($signalisationAnimateur);
    }

    /**
     * Get signalisationAnimateur
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignalisationAnimateur()
    {
        return $this->signalisationAnimateur;
    }

    /**
     * Add statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     *
     * @return Utilisateur
     */
    public function addStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique[] = $statistique;

        return $this;
    }

    /**
     * Remove statistique
     *
     * @param \Orange\MainBundle\Entity\Statistique $statistique
     */
    public function removeStatistique(\Orange\MainBundle\Entity\Statistique $statistique)
    {
        $this->statistique->removeElement($statistique);
    }
    
    /**
     * @return \Orange\MainBundle\Entity\Utilisateur
     */
    public function getSuperior() {
    	if($this->manager) {
    		$parent = $this->structure->getParent();
    		$superior = $parent ? $parent->getManager() : null;
    	} else {
    		$superior = $this->structure->getManager();
    	}
    	return $superior;
    }
    
    
    public function getCompletNom(){
    	return $this->prenom.' '.$this->nom;
    }
    
    /**
     * Check if is manager
     *
     * @return boolean
     */
    public function isManager()
    {
    	return $this->manager;
    }
    
    public function getService() {
    	return $this->structure ? $this->structure->getService() : null;
    }
    
    public function getDepartement(){
    	return $this->structure ? $this->structure->getDepartement() : null;
    }
    
    public function getDirection() {
    	return $this->structure ? $this->structure->getDirection() : null;
    }
    
    public function getPole() {
    	return $this->structure ? $this->structure->getPole() : null;
    }

    /**
     * Add contributeur
     * @param \Orange\MainBundle\Entity\Contributeur $contributeur
     * @return Utilisateur
     */
    public function addContributeur(\Orange\MainBundle\Entity\Contributeur $contributeur) {
        $this->contributeurs[] = $contributeur;
        return $this;
    }

    /**
     * Remove contributeur
     * @param \Orange\MainBundle\Entity\Contributeur $contributeur
     */
    public function removeContributeur(\Orange\MainBundle\Entity\Contributeur $contributeur) {
        $this->contributeurs->removeElement($contributeur);
    }

    /**
     * Get contributeurs
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributeurs() {
        return $this->contributeurs;
    }

    /**
     * Add actionAnimateur
     * @param \Orange\MainBundle\Entity\Action $actionAnimateur
     * @return Utilisateur
     */
    public function addActionAnimateur(\Orange\MainBundle\Entity\Action $actionAnimateur) {
        $this->actionAnimateur[] = $actionAnimateur;
        return $this;
    }

    /**
     * Remove actionAnimateur
     *
     * @param \Orange\MainBundle\Entity\Action $actionAnimateur
     */
    public function removeActionAnimateur(\Orange\MainBundle\Entity\Action $actionAnimateur)
    {
        $this->actionAnimateur->removeElement($actionAnimateur);
    }

    /**
     * Get actionAnimateur
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActionAnimateur()
    {
        return $this->actionAnimateur;
    }
    
    /**
     * @return array
     */
    public function getInstanceIdsForChefProjet() {
    	$ids = array();
    	foreach($this->getProjet() as $projet) {
    		$chantiers = $projet->getChantier();
    		foreach($chantiers as $chantier) {
    			array_push($ids, $chantier->getInstance()->getId());
    		}
    	}
    	return $ids;
    }
    
    public function getChildrenForStructure($structure){
    	$ids= array($structure->getId());
    	foreach ($structure->getChildren() as $child ){
    		array_push($ids, $child->getId());
    		foreach ($child->getChildren() as $c1 ){
    			array_push($ids, $c1->getId());
    			foreach ($c1->getChildren() as $c2 ){
    				array_push($ids, $c2->getId());
    			}
    		}
    	}
    	return $ids;
    }
    /**
     * @return array
     */
    public function getStructureIdsForRapporteur() {
    	$data = $this->getRapporteurStructure();
    	$result = array();
    	foreach($data as $structure) {
    		$result = array_merge($result, $this->getChildrenForStructure($structure));
    	}
    	return $result;
    }
    /**
     * @return array
     */
    public function getInstanceIds() {
    	$data = $this->getAnimators();
    	$ids = array();
    	foreach($data as $animator) {
    		array_push($ids, $animator->getInstance()->getId());
    	}
    	return count($ids)!=0 ? $ids : array(-1);
    }
    
    /**
     * @return array
     */
    public function getInstanceIdsForAdmin() {
    	$data = $this->structure->getBuPrincipal()->getInstance();
    	$ids = array();
    	foreach($data as $instance) {
    		array_push($ids, $instance->getId());
    	}
    	return $ids;
    }

    /**
     * Get statistique
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatistique()
    {
        return $this->statistique;
    }
    
    
    public function getPorteur(){
    	return $this->prenom.' '.$this->nom.' ['.$this->structure.']';
    }
    
    /**
     * Add reporting
     *
     * @param \Orange\MainBundle\Entity\Reporting $reporting
     * @return Utilisateur
     */
    public function addReporting(\Orange\MainBundle\Entity\Reporting $reporting)
    {
    	$this->reporting[] = $reporting;
    
    	return $this;
    }
    
    /**
     * Remove reporting
     *
     * @param \Orange\MainBundle\Entity\Reporting $reporting
     */
    public function removeReporting(\Orange\MainBundle\Entity\Reporting $reporting)
    {
    	$this->reporting->removeElement($reporting);
    }
    
    /**
     * Get reporting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReporting()
    {
    	return $this->reporting;
    }

    /**
     * Add actionGeneriqueHasStatut
     *
     * @param \Orange\MainBundle\Entity\ActionGeneriqueHasStatut $actionGeneriqueHasStatut
     *
     * @return Utilisateur
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
     * @return Utilisateur
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
     * Set canCreateActionGenerique
     *
     * @param boolean $canCreateActionGenerique
     *
     * @return Utilisateur
     */
    public function setCanCreateActionGenerique($canCreateActionGenerique)
    {
        $this->canCreateActionGenerique = $canCreateActionGenerique;

        return $this;
    }

    /**
     * Get canCreateActionGenerique
     *
     * @return boolean
     */
    public function getCanCreateActionGenerique()
    {
        return $this->canCreateActionGenerique;
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $action
     * @return boolean
     */
    public function isAnimatorOfAction($action) {
    	foreach($this->animators as $animateur) {
    		if($action->getInstance()->getId()==$animateur->getInstance()->getId()) {
    			return true;
    		}
    	}
    	return false;
    }
    
}

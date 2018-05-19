<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document
 * @ORM\Table(name="document")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Document
{
	
	static $motif;
	
	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="libelle", type="string", length=100, nullable=false)
	 */
	private $libelle;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nom_fichier", type="string", length=100, nullable=false)
	 */
	private $nomFichier;

	/**
	 * @var \DateTime
	 * @ORM\Column(name="date_creation", type="datetime", nullable=false)
	 */
	private $dateCreation;
	
	/**
	 * @var integer
	 * @ORM\Column(name="type", type="integer", length=1, nullable=false)
	 */
	private $type;
	
	/**
	 * @var \Orange\MainBundle\Entity\Action
	 * @ORM\ManyToOne(targetEntity="Action")
	 * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=true)
	 */
	private $action;
	
	/**
	 * @var \Orange\MainBundle\Entity\Tache
	 * @ORM\ManyToOne(targetEntity="Tache")
	 * @ORM\JoinColumn(name="tache_id", referencedColumnName="id", nullable=true)
	 */
	private $tache;
	
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 * @ORM\ManyToOne(targetEntity="Utilisateur")
	 * @ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=true)
	 */
	private $utilisateur;

    /**
     * @var UploadedFile
     * @Assert\File(maxSize="10000000", mimeTypesMessage="Veuiller choisir un fichier valide",
     * 		mimeTypes={"doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "gif", "jpg", "png", "eml", "msg"}
     * )
     * @Assert\NotNull(message="La fiche de renseignement est obligatoire")
     */
    public $file;
	
	/**
	 * Constructor
	 */
	public function __construct() {
	    $this->dateCreation = new \DateTime();
	}
	
	public function __toString(){
		return $this->libelle;
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
	 * Set nomFichier
	 * @param string $nomFichier
	 * @return Document
	 */
	public function setNomFichier($nomFichier) {
		$this->nomFichier = $nomFichier;
		return $this;
	}

	/**
	 * Set libelle
	 * @param string $libelle
	 * @return Document
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}

	/**
	 * Get libelle
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}

	/**
	 * Set dateCreation
	 * @param \DateTime $dateCreation
	 * @return Document
	 */
	public function setDateCreation($dateCreation) {
		$this->dateCreation = $dateCreation;
		return $this;
	}

	/**
	 * Get dateCreation
	 * @return \DateTime
	 */
	public function getDateCreation() {
		return $this->dateCreation;
	}
	
	/**
	 * Set motif
	 * @param integer $type
	 * @return Document
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 * Get type
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @return \Orange\MainBundle\Entity\Action
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Action $action
	 * @return \Orange\MainBundle\Entity\Document
	 */
	public function setAction($action) {
		$this->action = $action;
		return $this;
	}
	
	/**
	 * @return \Orange\MainBundle\Entity\Tache
	 */
	public function getTache() {
		return $this->tache;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Tache $tache
	 * @return \Orange\MainBundle\Entity\Document
	 */
	public function setTache($tache) {
		$this->tache = $tache;
		return $this;
	}
	
	/**
	 * @return \Orange\MainBundle\Entity\Utilisateur
	 */
	public function getUtilisateur() {
		return $this->utilisateur;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Utilisateur $utilisateur
	 * @return \Orange\MainBundle\Entity\Document
	 */
	public function setUtilisateur($utilisateur) {
		$this->utilisateur = $utilisateur;
		return $this;
	}
	
	/**
	 * @return UploadedFile
	 */
	public function getFile() {
		return $this->file;
	}
	
	/**
	 * @param UploadedFile $file
	 * @return Document
	 */
	public function setFile(UploadedFile $file) {
		$this->file = $file;
		return $this;
	}

    public function getAbsolutePath() {
        return null === $this->nomFichier ? null : $this->getUploadRootDir().'/'.$this->nomFichier;
    }

    public function getPath() {
        return $this->getWebPath();
    }

    public function getWebPath() {
        return null === $this->nomFichier ? null : $this->getUploadDir().'/'.$this->nomFichier;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir() {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir() {
        return 'upload'.($this->type ? '/erq' : null);
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->file) {
    		$this->libelle = $this->file->getClientOriginalName();
    		$toArray =explode('.', $this->libelle);
    		$extension= end($toArray);
            // faites ce que vous voulez pour générer un nom unique
            $this->nomFichier = sha1(uniqid(mt_rand(), true)).'.'.$extension;
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if (null === $this->file) {
            return;
        }
        $this->file->move($this->getUploadRootDir(), $this->nomFichier);
        unset($this->file);
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload() {
    	$file = $this->getAbsolutePath();
        if(file_exists($file)) {
            unlink($file);
        }
    }
	
	

    /**
     * Get nomFichier
     *
     * @return string
     */
    public function getNomFichier()
    {
        return $this->nomFichier;
    }
}

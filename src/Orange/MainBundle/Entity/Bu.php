<?php
namespace Orange\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Assetic\Exception\Exception;

/**
 * Bu
 *
 * @ORM\Table(name="bu")
 * @ORM\Entity(repositoryClass="Orange\MainBundle\Repository\BuRepository")
 */
class Bu
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
     * @var string
     * @ORM\Column(name="libelle", type="string", length=45, nullable=true)
     * @Assert\NotNull()
     */
    private $libelle;
    
    /**
     * @var string $validationAutomatique
     *
     * @ORM\Column(name="validation_automatique", type="boolean", nullable=true)
     */
    private  $validationAutomatique;

    /**
     * @var boolean
     *
     * @ORM\Column(name="demande_report", type="boolean", nullable=true)
     */
    private  $demandeReport;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="signalisation", type="boolean", nullable=true)
     */
    private  $signalisation;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Structure")
     * @ORM\JoinTable(name="structure_has_bu",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $structure;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Exception")
     * @ORM\JoinTable(name="bu_has_exception",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="exception_id", referencedColumnName="id")
     *   }
     * )
     */
    private $exception;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="FrequenceValidation")
     * @ORM\JoinTable(name="bu_has_frequence_validation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="frequence_validation_id", referencedColumnName="id")
     *   }
     * )
     */
    private $frequenceValidation;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Domaine", inversedBy="bu", cascade={"persist", "merge", "remove"})
     * @ORM\JoinTable(name="bu_has_domaine",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
     *   }
     * )
     */
    private $domaine;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Instance", inversedBy="bu")
     * @ORM\JoinTable(name="bu_has_instance",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instance_id", referencedColumnName="id")
     *   }
     * )
     */
    private $instance;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="TypeAction", inversedBy="bu")
     * @ORM\JoinTable(name="bu_has_type_action",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="type_action_id", referencedColumnName="id")
     *   }
     * )
     */
    private $typeAction;
    
    /**
     * @var string
     *
     * @ORM\Column(name="niveau_validation", type="string", length=45, nullable=true)
     * @Assert\NotNull()
     * 
     */
    private $niveauValidation;
    
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Structure", mappedBy="buPrincipal")
     */
    private $structureBuPrincipal;
    
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Structure")
     * @ORM\OrderBy({"lft" = "ASC"})
     * @ORM\JoinTable(name="structure_in_dashboard",
     *   joinColumns={
     *     @ORM\JoinColumn(name="bu_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="structure_id", referencedColumnName="id")
     *   }
     * )
     */
    private $structureInDashboard;
    
    /**
     * @var ParametrageBu
     * @ORM\OneToOne(targetEntity="ParametrageBu", mappedBy="bu", cascade={"persist", "merge", "remove"})
     */
    private $parametrage;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="BuHasConfig", mappedBy="bu")
     */
    private $configBu;
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->validationAutomatique = 0;
        $this->structure = new \Doctrine\Common\Collections\ArrayCollection();
        $this->exception = new \Doctrine\Common\Collections\ArrayCollection();
        $this->frequenceValidation = new \Doctrine\Common\Collections\ArrayCollection();
        $this->structureBuPrincipal = new \Doctrine\Common\Collections\ArrayCollection();
        $this->structureInDashboard = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instance= new \Doctrine\Common\Collections\ArrayCollection();
        $this->typeAction= new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString(){
    	return $this->libelle.'';
    }
    
    /**
     * @return integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getLibelle() {
		return $this->libelle;
	}
	
	/**
	 * @param string $libelle
	 * @return Bu
	 */
	public function setLibelle($libelle) {
		$this->libelle = $libelle;
		return $this;
	}
	
	
	/**
	 * @return Structure
	 */
	public function getRootStructure() {
		return $this->structure->filter(function($structure) {
				return $structure->getLvl()==0;
			});
	}

	/**
	 * @return Structure
	 */
	public function getStructure() {
		return $this->structure;
	}
	
    /**
     * Add structure
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Bu
     */
    public function addStructure(\Orange\MainBundle\Entity\Structure $structure) {
        $this->structure[] = $structure;
        return $this;
    }

    /**
     * Remove structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     */
    public function removeStructure(\Orange\MainBundle\Entity\Structure $structure) {
        $this->structure->removeElement($structure);
    }
    
    /**
     * @return Exception
     */
    public function getException() {
    	return $this->exception;
    }
    
    /**
     * Add exception
     * @param \Orange\MainBundle\Entity\Exception $exception
     * @return Bu
     */
    public function addException(\Orange\MainBundle\Entity\Exception $exception) {
    	$this->exception[] = $exception;
    	return $this;
    }
    
    /**
     * Remove exception
     *
     * @param \Orange\MainBundle\Entity\Exception $exception
     */
    public function removeException(\Orange\MainBundle\Entity\Exception $exception) {
    	$this->exception->removeElement($exception);
    }
	
    
    /**
     * @return FrequenceValidation
     */
    public function getFrequenceValidation() {
    	return $this->frequenceValidation;
    }
    
    /**
     * Add FrequenceValidation
     * @param \Orange\MainBundle\Entity\FrequenceValidation $frequenceValidation
     * @return Bu
     */
    public function addFrequenceValidation(\Orange\MainBundle\Entity\FrequenceValidation $frequenceValidation) {
    	$this->frequenceValidation[] = $frequenceValidation;
    	return $this;
    }
    
    /**
     * Remove FrequenceValidation
     *
     * @param \Orange\MainBundle\Entity\FrequenceValidation $frequenceValidation
     */
    public function removeFrequenceValidation(\Orange\MainBundle\Entity\FrequenceValidation $frequenceValidation) {
    	$this->frequenceValidation->removeElement($frequenceValidation);
    }
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getStructureInDashboard() {
		return $this->structureInDashboard;
	}
	
	public function getStructureInDashboardAsArray() {
		$data = array();
		foreach($this->getStructureInDashboard() as $structure) {
			$data[] = array('id' => $structure->getId(), 'libelle' => $structure->getLibelle());
		}
		return $data;
	}

    /**
     * Add structure
     * @param \Orange\MainBundle\Entity\Structure $structure
     * @return Bu
     */
    public function addStructureInDashboard(\Orange\MainBundle\Entity\Structure $structure) {
        $this->structureInDashboard->add($structure);
        return $this;
    }

    /**
     * Remove structure
     *
     * @param \Orange\MainBundle\Entity\Structure $structure
     */
    public function removeStructureInDashboard(\Orange\MainBundle\Entity\Structure $structure) {
        $this->structureInDashboard->removeElement($structure);
    }

    /**
     * Add domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     * @return Bu
     */
    public function addDomaine(\Orange\MainBundle\Entity\Domaine $domaine)
    {
        $this->domaine[] = $domaine;
        return $this;
    }

    /**
     * Remove domaine
     *
     * @param \Orange\MainBundle\Entity\Domaine $domaine
     */
    public function removeDomaine(\Orange\MainBundle\Entity\Domaine $domaine)
    {
        $this->domaine->removeElement($domaine);
    }

    /**
     * Get domaine
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Add instance
     *
     * @param \Orange\MainBundle\Entity\Instance $instance
     * @return Bu
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
     * Get ids instance
     * @return array 
     */
    public function getInstanceIds()
    {
    	$ids = array();
    	foreach($this->instance as $instance) {
    		$ids[] = $instance->getId();
    	}
        return count($ids)!=0 ? $ids : array(-1);
    }

    /**
     * Add typeAction
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     * @return Bu
     */
    public function addTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
        $this->typeAction[] = $typeAction;

        return $this;
    }

    /**
     * Remove typeAction
     *
     * @param \Orange\MainBundle\Entity\TypeAction $typeAction
     */
    public function removeTypeAction(\Orange\MainBundle\Entity\TypeAction $typeAction)
    {
        $this->typeAction->removeElement($typeAction);
    }

    /**
     * Get typeAction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypeAction()
    {
        return $this->typeAction;
    }

    /**
     * Set niveauValidation
     *
     * @param string $niveauValidation
     * @return Bu
     */
    public function setNiveauValidation($niveauValidation)
    {
        $this->niveauValidation = $niveauValidation;

        return $this;
    }

    /**
     * Get niveauValidation
     *
     * @return string 
     */
    public function getNiveauValidation()
    {
        return $this->niveauValidation;
    }

    /**
     * Add structureBuPrincipal
     *
     * @param \Orange\MainBundle\Entity\Structure $structureBuPrincipal
     *
     * @return Bu
     */
    public function addStructureBuPrincipal(\Orange\MainBundle\Entity\Structure $structureBuPrincipal)
    {
        $this->structureBuPrincipal[] = $structureBuPrincipal;

        return $this;
    }

    /**
     * Remove structureBuPrincipal
     *
     * @param \Orange\MainBundle\Entity\Structure $structureBuPrincipal
     */
    public function removeStructureBuPrincipal(\Orange\MainBundle\Entity\Structure $structureBuPrincipal)
    {
        $this->structureBuPrincipal->removeElement($structureBuPrincipal);
    }

    /**
     * Get structureBuPrincipal
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStructureBuPrincipal()
    {
        return $this->structureBuPrincipal;
    }

    /**
     * Set parametrage
     *
     * @param \Orange\MainBundle\Entity\ParametrageBu $parametrage
     *
     * @return Bu
     */
    public function setParametrage(\Orange\MainBundle\Entity\ParametrageBu $parametrage = null)
    {
        $this->parametrage = $parametrage;

        return $this;
    }

    /**
     * Get parametrage
     *
     * @return \Orange\MainBundle\Entity\ParametrageBu
     */
    public function getParametrage()
    {
        return $this->parametrage;
    }
	public function getDemandeReport() {
		return $this->demandeReport;
	}
	public function setDemandeReport($demandeReport) {
		$this->demandeReport = $demandeReport;
		return $this;
	}
	public function getValidationAutomatique() {
		return $this->validationAutomatique;
	}
	public function setValidationAutomatique($validationAutomatique) {
		$this->validationAutomatique = $validationAutomatique;
		return $this;
	}
	public function getSignalisation() {
		return $this->signalisation;
	}
	public function setSignalisation($signalisation) {
		$this->signalisation = $signalisation;
		return $this;
	}
	
    /**
     * Add configBu
     * @param \Orange\MainBundle\Entity\BuHasConfig $configBu
     * @return Bu
     */
    public function addConfigBu(\Orange\MainBundle\Entity\BuHasConfig $configBu)
    {
        $this->configBu[] = $configBu;

        return $this;
    }

    /**
     * Remove configBu
     * @param \Orange\MainBundle\Entity\BuHasConfig $configBu
     */
    public function removeConfigBu(\Orange\MainBundle\Entity\BuHasConfig $configBu)
    {
        $this->configBu->removeElement($configBu);
    }

    /**
     * Get configBu
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConfigBu()
    {
        return $this->configBu;
    }
    
    public function hasConfig($param) {
    	$result = $this->configBu->filter(function($data) use ($param){
    					return $data->getConfig()->getCode() == strtoupper($param);
                  });
    	return count($result)>0;
    }
    
    /**
     * Get valueConfig
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValueConfig($param) {
    	$result = $this->configBu->filter(function($data) use ($param){
    		return $data->getConfig()->getCode() == strtoupper($param);
    	});
    	return $result->count() > 0 ? $result->first()->getEtat() : false;
    }
}

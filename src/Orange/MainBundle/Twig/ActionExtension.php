<?php 
namespace Orange\MainBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerAware;

class ActionExtension extends \Twig_Extension {

	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	/**
	 * @var \Orange\QuickMakingBundle\Model\EntityManager
	 */
	private $em;
	
	/**
	 * @var array
	 */
	private $ids;
	
	/**
	 * @var array
	 */
	private $states;
	
	/**
	 * @var array
	 */
	private $types;

	/**
	 * @param ContainerAware $container
	 * @param array $ids
	 * @param array $states
	 * @param array $types
	 */
	public function __construct($container, $ids, $states, $types) {
		$this->twig = $container->get('twig');
		$this->em = $container->get('doctrine.orm.entity_manager');
		$this->ids = $ids;
		$this->states = $states;
		$this->types = $types;
	}
	
	/**
	 * {@inheritdoc}
	 */
    public function getFilters() {
        return array(
        	'number_actions' => new \Twig_Filter_Method($this, 'getNumberActions', array('is_safe' => array('html')))
        );
    }
    
    /**
     * @param Mixed $entity
     * @return number
     */
    public function getNumberActions($entity) {
    	if($entity instanceof \Orange\MainBundle\Entity\Espace) {
    		$number = $this->em->getRepository('OrangeMainBundle:Action')->getNumberByEspace($entity->getId());
    	} elseif($entity instanceof \Orange\MainBundle\Entity\Projet) {
    		$number = $this->em->getRepository('OrangeMainBundle:Action')->getNumberByProjet($entity->getId());
    	} elseif($entity instanceof \Orange\MainBundle\Entity\Chantier) {
    		$number = $this->em->getRepository('OrangeMainBundle:Action')->getNumberByChantier($entity->getId());
    	} else {
    		$number = 0;
    	}
    	return $number;
    }
    
    public function getName() {
        return 'action_extension';
    }
}
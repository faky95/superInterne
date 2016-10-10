<?php 
namespace Orange\MainBundle\Twig;


use Symfony\Component\DependencyInjection\ContainerAware;
use Orange\MainBundle\Entity\Statut;

class OrangeExtension extends \Twig_Extension {

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
            'show_status' => new \Twig_Filter_Method($this, 'showStatus', array('is_safe' => array('html'))),
            'get_statut' => new \Twig_Filter_Method($this, 'getStatutForAction', array('is_safe' => array('html'))),
        	'get_statut_signalisation' => new \Twig_Filter_Method($this, 'getStatutForSignalisation', array('is_safe' => array('html')))
        );
    }
    
    /**
     * @param string $entity
     * @param string $column
     */
    public function stateEntity($entity, $column) {
    	return $this->showStatus($entity, $column);
    }
    
    /**
     * @param string $entity
     * @param string $column
     */
    public function showStatus($entity, $column) {
    	if(!$entity) {
    		return;
    	}
    	$reflect = new \ReflectionClass($entity);
    	$template = $this->twig->loadTemplate('OrangeMainBundle:Extra:status.html.twig');
    	return $template->renderBlock('status_'.strtolower($reflect->getShortName().'_'.$column), array(
    			'entity' => $entity, 'ids' => $this->ids, 'states' => $this->states, 'types' => $this->types
    		));
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $entity
     */
    public function getStatutForAction($entity) {
    	return $this->em->getRepository('OrangeMainBundle:Statut')->getStatutForAction($entity);
    }
    
    /**
     *
     * @param \Orange\MainBundle\Entity\Signalisation $entity
     */
    public function getStatutForSignalisation($entity) {
    	return $this->em->getRepository ( 'OrangeMainBundle:Statut' )->getStatutForSignalisation ( $entity );
    }

    public function getName() {
        return 'orange_extension';
    }
}
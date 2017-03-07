<?php 
namespace Orange\MainBundle\Twig;

use Symfony\Component\Security\Core\SecurityContext;

class SecurityExtension extends \Twig_Extension
{
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	private $user;
	
    public function __construct($securityContext) {
        $this->user = $securityContext->getToken() ? $securityContext->getToken()->getUser() : null; 
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
        		new \Twig_SimpleFunction('has_rights', array($this, 'hasRights'), array('is_safe' => array('html')))
        	);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
        		'has_rights_animateur'	=> new \Twig_Filter_Method($this, 'hasRigthsAnimateur', array('is_safe' => array('html'))),
        		'has_rights_manager'	=> new \Twig_Filter_Method($this, 'hasRigthsManager', array('is_safe' => array('html')))
        	);
    }
    
    public function hasRights($role) {
    	if(is_array($role)) {
    		return $this->user ? $this->user->hasRoles($role) : false;
    	} else {
    		return $this->user ? $this->user->hasRole($role) : false;
    	}
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $entity
     * @return boolean
     */
    public function hasRigthsAnimateur($entity) {
    	$ids = array();
    	foreach($entity->getInstance()->getAnimateur() as $animateur) {
    		array_push($ids, $animateur->getUtilisateur()->getId());
    	}
    	return in_array($this->user->getId(), $ids);
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $entity
     * @return boolean
     */
    public function hasRigthsManager($entity) {
    	$manager = $entity->getPorteur()->getSuperior();
    	return $manager ? $manager->getId()==$this->user->getId() : false;
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'orange_main_extension';
    }
}

?>
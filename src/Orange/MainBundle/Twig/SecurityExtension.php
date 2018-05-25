<?php 
namespace Orange\MainBundle\Twig;

use Symfony\Component\Security\Core\SecurityContext;

class SecurityExtension extends \Twig_Extension
{
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	private $tokenStorage;
	
    public function __construct($container) {
    	$this->tokenStorage= $container->get('security.token_storage'); 
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
    	$user = $this->tokenStorage->getToken()->getUser();
    	if(is_array($role)) {
    		return $user ? $user->hasRoles($role) : false;
    	} elseif($role) {
    		return $user ? $user->hasRole($role) : false;
    	} else {
    		return $user ? true : false;
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
    	$user = $this->tokenStorage->getToken()->getUser();
    	return in_array($user->getId(), $ids);
    }
    
    /**
     * @param \Orange\MainBundle\Entity\Action $entity
     * @return boolean
     */
    public function hasRigthsManager($entity) {
    	$user = $this->tokenStorage->getToken()->getUser();
    	$manager = $entity->getPorteur()->getSuperior();
    	return $manager ? $manager->getId()==$user->getId() : false;
    }
    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'orange_security_extension';
    }
}

?>
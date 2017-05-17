<?php
namespace Orange\QuickMakingBundle\Twig;

use Orange\MainBundle\Utils\Notification;

class ParameterExtension extends \Twig_Extension
{

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('notification', array($this, 'getNotificationParameters'), array('is_safe' => array('html'))),
        );
    }

    public function getNotificationParameters($key)
    {
    	return array_key_exists($key, Notification::$notificationTypes) 
    		? Notification::$notificationTypes[$key]
    		: null;
    }
    

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'parameter';
    }
}

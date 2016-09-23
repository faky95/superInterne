<?php

namespace Orange\QuickMakingBundle\Twig;

class BreadCrumbExtension extends \Twig_Extension
{
    protected $controller;

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('breadCrumb', array($this, 'showBreadCrumb'), array('is_safe' => array('html'))),
        );
    }

    public function showBreadCrumb($path = array())
    {
    	$template = null;
    	foreach($path as $page=>$url) {
    		$this->addBreadCrumb($page, $url, $template);
    	}
        return sprintf('<ul class="breadcrumb">
		                	<li><i class="icon-home"></i>Accueil</li>%s
		                </ul>', $template
        	);
    }
    
    public function addBreadCrumb($page, $url, &$template) {
    	$template .= sprintf('<li><span class="divider">&raquo;</span><a href="%s">%s</a></li>', $url, $page);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'breadCrumb';
    }
}

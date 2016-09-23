<?php

namespace Orange\QuickMakingBundle\Twig;

class ModalExtension extends \Twig_Extension
{
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	public function __construct(\Twig_Environment $twig) {
		$this->twig = $twig;
	}

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('formModalSubmit', array($this, 'formModalSubmit'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('refreshEventModal', array($this, 'refreshEventModal'), array('is_safe' => array('html'))),
       		new \Twig_SimpleFunction('addEventActionLink', array($this, 'addEventActionLink'), array('is_safe' => array('html'))),
       		new \Twig_SimpleFunction('addEventTextTransformer', array($this, 'addEventTextTransformer'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('callEventActionLink', array($this, 'callEventActionLink'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('loadModalEvent', array($this, 'loadModalEvent'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('callValidationForm', array($this, 'callValidationForm'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('loadModal', array($this, 'loadModal'), array('is_safe' => array('html')))
        );
    }

    public function loadModal($idModal, $url) {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('load_modal', array('idModal' => $idModal, 'url' => $url));
    }

    public function addEventActionLink($functionAfterShowModal) {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('add_event_action_link', array('functionAfterShowModal' => $functionAfterShowModal));
    }

    public function addEventTextTransformer() {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('add_event_text_transformer', array());
    }

    public function formModalSubmit($idModal, $idLoading, $functionOnComplete) {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('add_form_submit_modal', array('idLoading' => $idLoading, 'functionOnComplete' => $functionOnComplete));
    }
    
    public function refreshEventModal() {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('form_submit_modal', array());
    }
    
    public function callValidationForm() {
    	$template = $this->twig->loadTemplate('OrangeQuickMakingBundle:Extra:modal.html.twig');
    	return $template->renderBlock('call_validation_form', array());
    }

    public function callEventActionLink() {
    	return sprintf("addEventActionLink();");
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'modal';
    }
}

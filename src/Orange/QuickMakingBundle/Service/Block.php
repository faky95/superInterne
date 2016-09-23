<?php

namespace Orange\QuickMakingBundle\Service;

Class Block {

    private $environment;

    public function __construct(\Twig_Environment $environment) {
        $this->environment = $environment;
    }

    public function render($template, $block, $data) {
        $template = $this->environment->loadTemplate($template);
        return $template->renderBlock($block, $data);
    }
}
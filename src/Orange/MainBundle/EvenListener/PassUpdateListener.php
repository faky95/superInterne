<?php

namespace Orange\MainBundle\EvenListener;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\Container;


class PassUpdateListener
{
    
    private $security_context;
    private $router;
    private $session;
    private $container;
    
    public function __construct(Router $router, SecurityContext $security_context, Session $session, Container $container)
    {
        $this->security_context = $security_context;
        $this->router 			= $router;
        $this->session 			= $session;
        $this->container 		= $container;
    }
    
    public function onCheckExpired(GetResponseEvent $event)
    {
    	$em = $this->container->get('doctrine.orm.entity_manager');
        if (($this->security_context->getToken()) && ($this->security_context->isGranted('IS_AUTHENTICATED_FULLY') ) ) 
        {
            $route_name = $event->getRequest()->get('_route');
            
            if ($route_name != 'first_change_password' && !$this->security_context->getToken()->getUser()->getFirstChangePassword()) 
            {
                	$response = new RedirectResponse($this->router->generate('first_change_password'));
                	$event->setResponse($response);
            }
        }
    }
    
    
}

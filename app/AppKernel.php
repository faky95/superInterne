<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        	new FOS\UserBundle\FOSUserBundle(),
        	new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
        	new Ob\HighchartsBundle\ObHighchartsBundle(),
        	new AppVentus\AlertifyBundle\AvAlertifyBundle(),
        	new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        	new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        	new FR3D\LdapBundle\FR3DLdapBundle(),
        	new FOS\RestBundle\FOSRestBundle(),
        	new JMS\SerializerBundle\JMSSerializerBundle(),
        	new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
        	new Nelmio\CorsBundle\NelmioCorsBundle(),
        	new Orange\QuickMakingBundle\OrangeQuickMakingBundle(),
        	new Orange\MainBundle\OrangeMainBundle()

        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * get WEB Dir
     */
    public function getWebDir() {
        return sprintf('%s/../web', $this->rootDir);
    }

    /**
     * Returns the kernel parameters.
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
    	return array_merge(parent::getKernelParameters(), array('kernel.web_dir' => $this->getWebDir()));
    }
}

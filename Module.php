<?php
namespace Zf2Interspire;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module implements BootstrapListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event)
    {

        $config = $event->getTarget()->getServiceManager()->get('Config');
        $config  = isset($config['interspire']) ? $config['interspire'] : array();

        # code
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig() {

        return array(
            'factories' => array(
                'interspire' => function (ServiceLocatorInterface $serviceLocator) {

                    $config = $serviceLocator->get('Config');
                    $config  = isset($config['interspire']) ? $config['interspire'] : array();

                    return new ApiClient($config);
                }
            ),
        );
    }

}

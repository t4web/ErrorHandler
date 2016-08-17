<?php

namespace T4web\ErrorHandler;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Exception\MissingDependencyModuleException;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    public function onBootstrap(EventInterface $mvcEvent)
    {
        $serviceManager = $mvcEvent->getApplication()->getServiceManager();

        /** @var ModuleManager $moduleManager */
        $moduleManager = $serviceManager->get('modulemanager');
        if (!$moduleManager->getModule('T4web\Log')) {
            throw new MissingDependencyModuleException('Module "T4web\Log" must be enabled in your
                config/application.config.php. For details see https://github.com/t4web/ErrorHandler#post-installation.');
        }

        /** @var ErrorHandler $errorHandler */
        $errorHandler = $serviceManager->get(ErrorHandler::class);

        $mvcEvent->getApplication()->getEventManager()->attach(
            [
                MvcEvent::EVENT_DISPATCH_ERROR,
                MvcEvent::EVENT_RENDER_ERROR,
            ],
            function(MvcEvent $mvcEvent) use ($errorHandler) {

                if ($mvcEvent->getError() == Application::ERROR_EXCEPTION) {
                    /** @var \Exception $exception */
                    $exception = $mvcEvent->getParam('exception');
                    while ($exception->getPrevious()) {
                        $exception = $exception->getPrevious();
                    }

                    $errorHandler->handleException($exception);
                }
            }
        );

        register_shutdown_function([$errorHandler, 'onShutdown']);
        set_error_handler([$errorHandler, 'handle']);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include dirname(__DIR__) . '/config/module.config.php';
    }
}

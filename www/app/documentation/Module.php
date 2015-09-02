<?php

namespace Baseapp\Documentation;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Class Module
 * @package Baseapp\Documentation
 */

class Module implements ModuleDefinitionInterface
{

    /**
     * Register a specific autoloader for the module
     *
     *
     * @param mixed $di dependency Injector
     *
     * @return void
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new \Phalcon\Loader();

        $loader->registerNamespaces(array(
            'Baseapp\Documentation\Controllers' => __DIR__ . '/controllers/',
        ));

        $loader->register();
    }

    /**
     * Register specific services for the module
     *
     *
     * @param object $di dependency Injector
     *
     * @return void
     */
    public function registerServices(DiInterface $di)
    {
        //Registering a dispatcher
        $di->set('dispatcher', function() {
            //Create/Get an EventManager
            $eventsManager = new \Phalcon\Events\Manager();
            //Attach a listener
            $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {
                //controller or action doesn't exist
                if ($event->getType() == 'beforeException') {
                    switch ($exception->getCode()) {
                        case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                            $dispatcher->forward(array(
                                'controller' => 'index',
                                'action' => 'notFound'
                            ));
                            return false;
                    }
                }
            });

            $dispatcher = new \Phalcon\Mvc\Dispatcher();
            //Set default namespace to documentation module
            $dispatcher->setDefaultNamespace("Baseapp\Documentation\Controllers");
            //Bind the EventsManager to the dispatcher
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        });

        //Registering the view component
        $di->set('view', function() use($di) {

            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(__DIR__ . '/views/');
           // $view->setLayoutsDir('../frontend/views/');
            $view->registerEngines(\Baseapp\Library\Tool::registerEngines($view, $di));
            return $view;
        });
    }

}

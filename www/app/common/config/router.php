<?php
/**
 * Created by PhpStorm.
 * User: Al
 * Date: 02/08/2015
 * Time: 17:22
 */
$router = new \Phalcon\Mvc\Router(FALSE);


$router->setDefaults(array(
    'module' => 'frontend',
    'controller' => 'index',
    'action' => 'index'
));

/*
 * All defined routes are traversed in reverse order until Phalcon\Mvc\Router
 * finds the one that matches the given URI and processes it, while ignoring the rest.
 */
$frontend = new \Phalcon\Mvc\Router\Group(array(
    'module' => 'frontend',
));
$frontend->add('/:controller/:action/:params', array(
    'controller' => 1,
    'action' => 2,
    'params' => 3,
));
$frontend->add('/:controller/:int', array(
    'controller' => 1,
    'id' => 2,
));
$frontend->add('/:controller[/]?', array(
    'controller' => 1,
));
$frontend->add('/{action:(buy|contact|terms)}[/]?', array(
    'controller' => 'static',
    'action' => 'action'
));
$frontend->add('/');

// Mount a group of routes for frontend
$router->mount($frontend);



/**
 * Define routes for each module
 */
//foreach ($this->getModules() as $module => $options) {
foreach (array('backend' => array('alias' => \Phalcon\DI::getDefault()->getShared('config')->app->admin_uri), 'documentation' => array('alias' => 'doc')) as $module => $options) {
    $group = new \Phalcon\Mvc\Router\Group(array(
        'module' => $module,
    ));
    $group->setPrefix('/' . (isset($options['alias']) ? $options['alias'] : $module));

    $group->add('/:controller/:action/:params', array(
        'controller' => 1,
        'action' => 2,
        'params' => 3,
    ));
    $group->add('/:controller/:int', array(
        'controller' => 1,
        'id' => 2,
    ));
    $group->add('/:controller[/]?', array(
        'controller' => 1,
    ));
    $group->add('[/]?', array());

    // Mount a group of routes for some module
    $router->mount($group);
}

$router->notFound(array(
    'controller' => 'index',
    'action' => 'notFound'
));

return $router;
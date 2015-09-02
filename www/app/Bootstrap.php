<?php

namespace Baseapp;

use Baseapp\Library\Auth;
use Baseapp\Library\I18n;
use Baseapp\Library\Email;
use Phalcon\Debug\Dump;



/**
 * Phalcon Rocket Bootstrap
 */
class Bootstrap extends \Phalcon\Mvc\Application
{

    private $_di;
    private $_config;
    private $_profiler;

    /**
     * Bootstrap constructor - set the dependency Injector
     *
     * @param \Phalcon\DiInterface $di
     */
    public function __construct(\Phalcon\DiInterface $di)
    {
        $this->startTime = microtime();
        $this->_di = $di;

        $loaders = array('config', 'loader', 'timezone', 'i18n', 'db', 'filter', 'flash', 'crypt', 'auth', 'session', 'cookie', 'cache', 'url', 'router');

        // Register services
        foreach ($loaders as $service) {
            $this->$service();
        }

        // Register modules
        $this->registerModules(array(
            'frontend' => array(
                'className' => 'Baseapp\Frontend\Module',
                'path' => APP_PATH . '/app/frontend/Module.php'
            ),
            'backend' => array(
                'className' => 'Baseapp\Backend\Module',
                'path' => APP_PATH . '/app/backend/Module.php',
                'alias' => 'admin'
            ),
            'documentation' => array(
                'className' => 'Baseapp\Documentation\Module',
                'path' => APP_PATH . '/app/documentation/Module.php',
                'alias' => 'doc'
            )
        ));

        // Register the app itself as a service
        $this->_di->set('app', $this);

        // Set the dependency Injector
        parent::__construct($this->_di);
    }



    /**
     * Register an autoloader
     */
    protected function loader()
    {
        $loader = new \Phalcon\Loader();
        $loader->registerNamespaces(array(
            'Baseapp\Models' => APP_PATH . '/app/common/models/',
            'Baseapp\Library' => APP_PATH . '/app/common/library/',
            'Baseapp\Extension' => APP_PATH . '/app/common/extension/'
        ))->register();
    }

    /**
     * Set the config service
     *
     * @return void
     */
    protected function config()
    {
        $config = new \Phalcon\Config\Adapter\Ini(APP_PATH . '/app/common/config/config.ini');
        $this->_di->set('config', $config);
        if($this->config->app->env == 'development'){
            $dev = new \Phalcon\Config\Adapter\Ini(APP_PATH . '/app/common/config/development.ini');
            $config->merge($dev);
        }else{
            $prod = new \Phalcon\Config\Adapter\Ini(APP_PATH . '/app/common/config/production.ini');
            $config->merge($prod);
        }
        $this->_config = $config;
    }

    /**
     * Set the time zone
     *
     * @return void
     */
    protected function timezone()
    {
        date_default_timezone_set($this->_config->app->timezone);
    }

    /**
     * Set the language service
     *
     * @return void
     */
    protected function i18n()
    {
        $this->_di->setShared('i18n', function() {
            return I18n::instance();
        });
    }

    /**
     * Set the security service
     *
     * @return void
     */
    protected function security()
    {
        $config = $this->_config;
        $this->_di->set('security', function() use ($config) {
            $security = new \Phalcon\Security();
            $security->setWorkFactor($config->auth->hash_workload);
            $security->setDefaultHash(\Phalcon\Security::CRYPT_BLOWFISH_Y);
            return $security;
        });
    }

    /**
     * Set the crypt service
     *
     * @return void
     */
    protected function crypt()
    {
        $config = $this->_config;
        $this->_di->set('crypt', function() use ($config) {
            $crypt = new \Phalcon\Crypt();
            $crypt->setKey($config->crypt->key);
            $crypt->setPadding(\Phalcon\Crypt::PADDING_ZERO);
            return $crypt;
        });
    }

    /**
     * Set the auth service
     *
     * @return void
     */
    protected function auth()
    {
        $this->_di->setShared('auth', function() {
            return Auth::instance();
        });
    }

    /**
     * Set the filter service
     *
     * @return void
     */
    protected function filter()
    {
        $this->_di->set('filter', function() {
            $filter = new \Phalcon\Filter();
            $filter->add('repeat', new Extension\Repeat());
            $filter->add('escape', new Extension\Escape());
            return $filter;
        });
    }

    /**
     * Set the cookie service
     *
     * @return void
     */
    protected function cookie()
    {
        $this->_di->set('cookies', function() {
            $cookies = new \Phalcon\Http\Response\Cookies();
            return $cookies;
        });
    }



    /**
     * Profiler
     */
    protected function profiler(){
        $this->_profiler = new \Phalcon\Db\Profiler();
        $profiler = $this->_profiler;
        $this->_di->set('profiler', function() use ($profiler) {
            return $profiler;
        });
    }



    /**
     * Set the database service
     * @return void
     */
    protected function db()
    {

        $config = $this->_config;
        $profiler = $this->profiler();
        //@todo get this sodding profiler working
        $this->_di->set('db', function() use ($config, $profiler) {
            $eventsManager = new \Phalcon\Events\Manager();
            // Listen to all database events
            $eventsManager->attach('db', function ($event, $connection) use ($profiler) {
                /*$profiler = new \Phalcon\Db\Profiler();
                //var_dump($profiler); exit;
                if ($event->getType() == 'beforeQuery') {
                    $profiler->startProfile($connection->getSQLStatement());
                }

                if ($event->getType() == 'afterQuery') {
                    $profiler->stopProfile();
                }*/
            });

            $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                "host" => $config->database->host,
                "username" => $config->database->username,
                "password" => $config->database->password,
                "dbname" => $config->database->dbname,
                "options" => array(
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                )
            ));
            $connection->setEventsManager($eventsManager);
            return $connection;
        });

    }






    /**
     * Set the flash service
     *
     * @return void
     */
    protected function flash()
    {
        $this->_di->set('flashSession', function() {
            $flash = new \Phalcon\Flash\Session(array(
                'warning' => 'ui warning message',
                'notice' => 'ui info message',
                'success' => 'ui positive message',
                'error' => 'ui negative message',
                'message' => 'ui message',
            ));
            return $flash;
        });
    }

    /**
     * Set the session service
     *
     * @return void
     */
    protected function session()
    {
        $this->_di->set('session', function() {
            $session = new \Phalcon\Session\Adapter\Files();
            $session->start();
            return $session;
        });
    }

    /**
     * Set the cache service
     *
     * @return void
     */
    protected function cache()
    {
        $config = $this->_config;
        foreach ($config->cache->services as $service => $section) {
            $this->_di->set($service, function() use ($config, $section) {
                // Load settings for some section
                $frontend = $config->$section;
                $backend = $config->{$frontend->backend};

                // Set adapters
                $adapterFrontend = "\Phalcon\Cache\Frontend\\" . $frontend->adapter;
                $adapterBackend = "\Phalcon\Cache\Backend\\" . $backend->adapter;

                // Set cache
                $frontCache = new $adapterFrontend($frontend->options->toArray());
                $cache = new $adapterBackend($frontCache, $backend->options->toArray());
                return $cache;
            });
        }
    }

    /**
     * Set the url service
     *
     * @return void
     */
    protected function url()
    {
        $config = $this->_config;
        $this->_di->set('url', function() use ($config) {
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri($config->app->base_uri);
            $url->setStaticBaseUri($config->app->static_uri);
            return $url;
        });
    }

    /**
     * Set the static router service
     *
     * @return void
     */
    protected function router()
    {
        $this->_di->set('router', function() {
            return require APP_PATH . '/app/common/config/router.php';
        });
    }

    /**
     * HMVC request in the application
     *
     * @param array $location location to run the request
     *
     * @return mixed response
     */
    public function request($location)
    {
        $dispatcher = clone $this->getDI()->get('dispatcher');

        if (isset($location['controller'])) {
            $dispatcher->setControllerName($location['controller']);
        } else {
            $dispatcher->setControllerName('index');
        }

        if (isset($location['action'])) {
            $dispatcher->setActionName($location['action']);
        } else {
            $dispatcher->setActionName('index');
        }

        if (isset($location['params'])) {
            if (is_array($location['params'])) {
                $dispatcher->setParams($location['params']);
            } else {
                $dispatcher->setParams((array) $location['params']);
            }
        } else {
            $dispatcher->setParams(array());
        }

        $dispatcher->dispatch();

        $response = $dispatcher->getReturnedValue();
        if ($response instanceof \Phalcon\Http\ResponseInterface) {
            return $response->getContent();
        }

        return $response;
    }





    /**
     * Log message into file, notify the admin on stagging/production
     *
     * @param mixed $messages messages to log
     */
    public static function log($messages)
    {
        $config = \Phalcon\DI::getDefault()->getShared('config');
        $dump = new Dump();
        if ($config->app->env == "development") {
            foreach ($messages as $key => $message) {
                echo $dump->one($message, $key);
            }
            exit();
        } else {
            $logger = new \Phalcon\Logger\Adapter\File(APP_PATH . '/app/common/logs/' . date('Ymd') . '.log', array('mode' => 'a+'));
            $log = '';

            if (is_array($messages) || $messages instanceof \Countable) {
                foreach ($messages as $key => $message) {
                    if (in_array($key, array('alert', 'debug', 'error', 'info', 'notice', 'warning'))) {
                        $logger->$key($message);
                    } else {
                        $logger->log($message);
                    }
                    $log .= $dump->one($message, $key);
                }
            } else {
                $logger->log($messages);
                $log .= $dump->one($messages);
            }

            if ($config->app->env != "testing") {
                $email = new Email();
                $email->prepare(__('Something is wrong!'), $config->app->admin, 'error', array('log' => $log));

                if ($email->Send() !== true) {
                    $logger->log($email->ErrorInfo);
                }
            }

            $logger->close();
        }
    }

    /**
     * Catch the exception and log it, display pretty view
     *
     * @param \Exception $e
     */
    public static function exception(\Exception $e)
    {
        $config = \Phalcon\DI::getDefault()->getShared('config');
        $errors = array(
            'error' => get_class($e) . '[' . $e->getCode() . ']: ' . $e->getMessage(),
            'info' => $e->getFile() . '[' . $e->getLine() . ']',
            'debug' => "Trace: \n" . $e->getTraceAsString() . "\n",
        );

        if ($config->app->env == "development") {
            // Display debug output
            $debug = new \Phalcon\Debug();
            $debug->onUncaughtException($e);

        } else {
            // Display pretty view of the error
            $di = new \Phalcon\DI\FactoryDefault();
            $view = new \Phalcon\Mvc\View\Simple();
            $view->setDI($di);
            $view->setViewsDir(APP_PATH . '/app/frontend/views/');
            $view->registerEngines(\Baseapp\Library\Tool::registerEngines($view, $di));
            echo $view->render('error', array('i18n' => I18n::instance(), 'config' => $config));

            // Log errors to file and send email with errors to admin
            \Baseapp\Bootstrap::log($errors);
        }
    }


}

<?php

namespace Baseapp\Backend\Controllers;

/**
 * Backend Index Controller
 *
 */
class IndexController extends \Phalcon\Mvc\Controller
{

    public $scripts = array();

    /**
     * Before Action
     *
     */
    public function beforeExecuteRoute($dispatcher)
    {
        // Set default title
        $this->tag->setTitle('Index');
        $this->pageName = 'admin';

        // Add css and js to assets collection
        $this->assets->addCss('css/app.css');
       // $this->assets->addJs('js/plugins.js');
        $this->assets->addJs('js/app.js');
    }

    /**
     * Initialize
     *
     */
    public function initialize()
    {
        // Display 404 page if user is not admin - this helps to hide the true admin url
        if (!$this->auth->logged_in('admin')) {
            return $this->notfoundAction();
        }

        // Check the session lifetime
        if ($this->session->has('last_active') && time() - $this->session->get('last_active') > $this->config->session->options->lifetime) {
            $this->session->destroy();
            //$this->auth->refresh_session();
        }

        $this->session->set('last_active', time());

        // Set the language from session
        if ($this->session->has('lang')) {
            $this->i18n->lang($this->session->get('lang'));
            // Set the language from cookie
        } elseif ($this->cookies->has('lang')) {
            $this->i18n->lang($this->cookies->get('lang')->getValue());
        }

        // Send langs to the view
        $this->view->setVars(array(
            // Translate langs before
            'siteLangs' => array_map('__', $this->config->i18n->langs->toArray())
        ));
    }

    /**
     * Index Action
     *
     */
    public function indexAction()
    {
        $this->tag->setTitle(__('Admin panel'));

        $this->tag->setTitle(__('Admin panel'));

        /**
         * This code will benchmark your server to determine how high of a cost you can
         * afford. You want to set the highest cost that you can without slowing down
         * you server too much. 8-10 is a good baseline, and more is good if your servers
         * are fast enough. The code below aims for ≤ 50 milliseconds stretching time,
         * which is a good baseline for systems handling interactive logins.
         */
        $timeTarget = 0.05; // 50 milliseconds

        $costPhp = 8;
        do {
            $costPhp++;
            $start = microtime(true);
            password_hash("test", PASSWORD_BCRYPT, ["cost" => $costPhp]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);

        //echo "Appropriate Cost Found: " . $cost . "\n";
        $this->view->setVar('costPhp', $costPhp);

        $costPhal = 8;
        do{
            $costPhal++;
            $start = microtime(true);
            $security = new \Phalcon\Security();
            //$security->setDefaultHash($this->config->security->key);
            $security->setWorkFactor($costPhal);
            $security->setDefaultHash(\Phalcon\Security::CRYPT_BLOWFISH_Y);
            $security->hash("test");
            $end = microtime(true);
        }while(($end - $start) < $timeTarget);

        $this->view->setVar('costPhal', $costPhal);



    }



    /**
     * After Action
     *
     */
    public function afterExecuteRoute($dispatcher)
    {
        // Set final title
        $this->tag->setTitleSeparator(' | ');
        $this->tag->appendTitle($this->config->app->name);

        $this->view->setVars(array(
            'scripts' => $this->scripts,
            'pageName' => $this->pageName,
            'base_url' => $this->config->app->base_uri,
            'admin_url' => $this->config->app->admin_uri . '/'
        ));

        // Set scripts
//        $this->view->setVar('scripts', $this->scripts);

        // Minify css and js collection
        \Baseapp\Library\Tool::assetsMinification();
    }

    /**
     * Not found Action
     *
     */
    public function notfoundAction()
    {
        // Send a HTTP 404 response header
        $this->response->setStatusCode(404, "Not Found");
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $this->view->setMainView('404');
        $this->assets->addCss('css/fonts.css');
    }

}

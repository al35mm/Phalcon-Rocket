<?php

namespace Baseapp\Frontend\Controllers;


/**
 * Frontend Index Controller
 */
class IndexController extends \Phalcon\Mvc\Controller
{

    public $siteDesc;
    public $scripts = array();

    /**
     * Before Action
     */
    public function beforeExecuteRoute($dispatcher)
    {
        // Set default title and description
        $this->tag->setTitle($this->config->app->name);
        $this->siteDesc = 'Default';
        $this->pageName = '';

        // Add css and js to assets collection
        $this->assets->addCss('css/app.css');
        $this->assets->addJs('js/app.js');
    }

    /**
     * Initialize
     */
    public function initialize()
    {
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

        // Mobile detect
        $mobile_detect = new \Baseapp\Library\Mobile_Detect;
        $is_mobile = $mobile_detect->isMobile();
        $is_table = $mobile_detect->isTablet();
        $this->view->setVars(array(
            'is_mobile' => $is_mobile,
            'is_tablet' => $is_table,
            'mobile_detect' => $mobile_detect
        ));
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->tag->setTitle(__('Home'));
        $this->siteDesc = __('Home');
        $this->pageName = 'home';
    }

    /**
     * After Action
     */
    public function afterExecuteRoute($dispatcher)
    {
        // Set final title and description
        $this->tag->setTitleSeparator(' | ');
        $this->tag->appendTitle($this->config->app->name);
        $this->view->setVars(array(
            'app_name' => $this->config->app->name,
            'siteDesc' => mb_substr($this->filter->sanitize($this->siteDesc, 'string'), 0, 200, 'utf-8'),
            'scripts'  => $this->scripts,
            'pageName' => $this->pageName,
            'base_url' => $this->config->app->base_uri
        ));
//        $this->view->setVar('siteDesc', mb_substr($this->filter->sanitize($this->siteDesc, 'string'), 0, 200, 'utf-8'));

        // Set scripts
//        $this->view->setVar('scripts', $this->scripts);

        // Minify css and js collection
        \Baseapp\Library\Tool::assetsMinification();
    }

    /**
     * Not found Action
     */
    public function notFoundAction()
    {
        // Send a HTTP 404 response header
        $this->response->setStatusCode(404, "Not Found");
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
        $this->view->setMainView('404');
        $this->assets->addCss('css/fonts.css');
    }

}

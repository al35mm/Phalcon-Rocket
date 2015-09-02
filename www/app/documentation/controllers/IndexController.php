<?php

namespace Baseapp\Documentation\Controllers;

/**
 * Class IndexController
 * @package Baseapp\Documentation\Controllers
 */

class IndexController extends \Phalcon\Mvc\Controller
{

    public $siteDesc;
    public $scripts = array();

    /**
     * Before Action
     *
     */
    public function beforeExecuteRoute($dispatcher)
    {
        // Set default title and description
        $this->tag->setTitle('Documentation');
        $this->siteDesc = 'Documentation';
        $this->pageName = 'docs';

        // Add css and js to assets collection
        $this->assets->addCss('css/fonts.css');
        $this->assets->addCss('css/app.css');
        $this->assets->addCss('css/highlight.arta.css');
        //$this->assets->addJs('js/plugins.js');
        $this->assets->addJs('js/app.js');
        $this->assets->addJs('js/plugins/highlight.pack.js');
    }

    /**
     * Initialize
     *
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

        // Send i18n, auth and langs to the view
        $this->view->setVars(array(
            'auth' => $this->auth,
            'i18n' => $this->i18n,
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

    }


    /**
     * After Action
     *
     */
    public function afterExecuteRoute($dispatcher)
    {
        // Set final title and description
        $this->tag->setTitleSeparator(' | ');
        $this->tag->appendTitle($this->config->app->name);
        // Set scripts
        $scripts = array('$(document).ready(function() { $("pre code").each(function(i, e) {hljs.highlightBlock(e)}); });');

        $this->view->setVars(array(
            'siteDesc' => mb_substr($this->filter->sanitize($this->siteDesc, 'string'), 0, 200, 'utf-8'),
            'scripts' => array_merge($this->scripts, $scripts),
            'pageName' => $this->pageName,
            'base_url' => $this->config->app->base_uri
        ));

//        $this->view->setVar('siteDesc', mb_substr($this->filter->sanitize($this->siteDesc, 'string'), 0, 200, 'utf-8'));


//        $this->view->setVar('scripts', array_merge($this->scripts, $scripts));

        // Minify css and js collection
        \Baseapp\Library\Tool::assetsMinification();
    }

    /**
     * Not found Action
     *
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

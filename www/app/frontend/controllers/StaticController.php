<?php

namespace Baseapp\Frontend\Controllers;


/**
 * Static Payment Controller
 */
class StaticController extends IndexController
{

    /*** Terms & Conditions ***/
    public function termsAction(){
        $this->tag->setTitle(__('Terms Of Use'));
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_ACTION_VIEW);
    }



    /**
     * Contact Action
     */
    public function contactAction()
    {
        $this->tag->setTitle(__('Contact'));

        $this->assets->addJs('js/forms.js');


        if ($this->request->isPost() === true) {
            $validation = new \Baseapp\Extension\Validation();

            $validation->add('fullName', new \Phalcon\Validation\Validator\PresenceOf());
            $validation->add('content', new \Phalcon\Validation\Validator\PresenceOf());
            $validation->add('content', new \Phalcon\Validation\Validator\StringLength(array(
                'max' => 5000,
                'min' => 10,
            )));
            $validation->add('email', new \Phalcon\Validation\Validator\PresenceOf());
            $validation->add('email', new \Phalcon\Validation\Validator\Email());
            $validation->add('repeatEmail', new \Phalcon\Validation\Validator\Confirmation(array(
                'with' => 'email',
            )));
            // Recaptcha validation
            if($this->config->recaptcha->enabled == '1') {
                $validation->add('g-recaptcha-response', new \Baseapp\Extension\Recaptcha());
            }

            $validation->setLabels(array('fullName' => __('Full name'), 'content' => __('Content'), 'email' => __('Email'), 'repeatEmail' => __('Repeat email')));
            $messages = $validation->validate($_POST);



            if (count($messages)) {
                $this->view->setVar('errors', $validation->getMessages());
                $this->flashSession->warning('<i class="close icon"></i><div class="header">' . __('Warning') . '!</div> ' . __("Please correct the errors."));
            } else {
                $this->flashSession->success('<i class="close icon"></i><div class="header">' . __('Success') . '!</div> ' . __("Message was sent"));

                $email = new \Baseapp\Library\Email();
                $email->prepare(__('Contact'), $this->config->app->admin, 'contact', array(
                    'fullName' => $this->request->getPost('fullName'),
                    'email' => $this->request->getPost('email'),
                    'content' => $this->request->getPost('content'),
                ));
                $email->addReplyTo($this->request->getPost('email'));

                if ($email->Send() === true) {
                    unset($_POST);
                } else {
                    \Baseapp\Bootstrap::log($email->ErrorInfo);
                }
            }
        }
    }



}

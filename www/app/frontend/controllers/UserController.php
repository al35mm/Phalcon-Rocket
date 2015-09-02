<?php

namespace Baseapp\Frontend\Controllers;

use Baseapp\Models\Users;

/**
 * Frontend User Controller
 */
class UserController extends IndexController
{

    /**
     * Index Action
     */
    public function indexAction()
    {
        if ($this->auth->logged_in()) {
            $this->pageName = 'account';

            if (Users::getUserRoles($this->auth->get_user()->id, 'unconfirmed')) {
                $this->flashSession->warning(
                    '<i class="close icon"></i>' .
                    '<div class="ui header">Email activation required!</div> ' .
                    '<div class="content">We sent you an email with instructions on how to activate your account.
                    Please check your inbox. If you did not receive the email, you can <a href="' . $this->config->app->base_uri . 'user/resend/">get another one sent here</a>.</div>');
            }

            if ($this->request->isPost()) {
                $validation = new \Baseapp\Extension\Validation();
                if ($this->request->getPost('email')) {
                    $validation->add('email', new \Phalcon\Validation\Validator\Email());
                    $validation->add('email', new \Baseapp\Extension\Uniqueness(array(
                        'model' => '\Baseapp\Models\Users',
                    )));
                    $validation->setLabels(array('email' => __('Email')));
                    $messages = $validation->validate($_POST);
                    if (count($messages)) {
                        $errors = $validation->getMessages();
                        $this->view->setVar('errors', $errors);
                    } else {
                        $users = new Users;
                        if ($users->resend($this->auth->get_user()->id, $this->request->getPost('email', 'email'))) {
                            $this->flashSession->notice(
                                '<i class="close icon"></i>' .
                                '<div class="ui header">Email activation required!</div> ' .
                                '<div class="content">Please confirm your email address by folowing the instructions in the email we just sent you.
                    Please check your inbox. If you did not receive the email, you can <a href="' . $this->config->app->base_uri . 'user/resend/">get another one sent here</a>.</div>');
                        }
                    }
                }
                if ($this->request->getPost('password')) {
                    $validation->add('password', new \Baseapp\Extension\Password());
                    $validation->add('repeatPassword', new \Phalcon\Validation\Validator\Confirmation(array(
                        'with' => 'password',
                    )));

                    $validation->setLabels(array('password' => __('Password'), 'repeatPassword' => __('Repeat password')));
                    $messages = $validation->validate($_POST);
                    if (count($messages)) {
                        $errors = $validation->getMessages();
                        $this->view->setVar('errors', $errors);
                    } else {
                        $users = new Users;
                        if ($users->editPassword($this->auth->get_user()->id, $this->request->getPost('password'))) {
                            $this->flashSession->success(
                                '<i class="close icon"></i>' .
                                '<div class="ui header">Password changed!</div> ' .
                                '<div class="content">Your password has been updated.</div>');
                        }
                    }
                }
            }

        } else {
            $this->view->pick('msg');
            $this->tag->setTitle(__('No access'));

            $this->view->setVar('title', __('No access'));
            $this->view->setVar('redirect', 'user/signin');
            $this->flashSession->error($this->tag->linkTo(array('#', 'class' => 'close', 'title' => __("Close"), '×')) . '<strong>' . __('Error') . '!</strong> ' . __("Please log in to access."));
        }
    }


    /**
     * Sign in Action
     */
    public function signinAction()
    {
        if ($this->auth->logged_in()) {
            return $this->response->redirect('user/');
        }
        $this->tag->setTitle('Log in');
        $this->assets->addJs('js/forms.js');

        $attempts_state = $this->auth->loginAttempts(FALSE, 'state');
        $this->view->setVar('attempts', $attempts_state);

        if ($this->request->hasPost('submit_signin') && $this->request->hasPost('username') && $this->request->hasPost('password')) {

            // check the CSRF security token
            if ( ! $this->security->checkToken()) {
                $this->flashSession->warning(
                    '<i class="close icon"></i>' .
                    '<div class="ui header">Security failed!</div> ' .
                    '<div class="content">Please try again.</div>');
                return $this->response->redirect('user/signin/');
            }

            $validation = new \Baseapp\Extension\Validation();
            $validation->add('username', new \Phalcon\Validation\Validator\PresenceOf());
            $validation->add('password', new \Phalcon\Validation\Validator\PresenceOf());
            // Recaptcha check
            if ($attempts_state['captcha'] == TRUE && $this->config->recaptcha->enabled == '1') {
                $validation->add('g-recaptcha-response', new \Baseapp\Extension\Recaptcha());
            }
            $messages = $validation->validate($_POST);

            $login = FALSE;
            if (count($messages)) {
                $errors = $validation->getMessages();
                $this->view->setVar('errors', $errors);
            }else {
                $login = $this->auth->login($this->request->getPost('username'), $this->request->getPost('password'), $this->request->getPost('rememberMe') ? TRUE : FALSE);


                if (!$login) {
                    $this->flashSession->error('<i class="close icon"></i><div class="header"><i class="warning sign icon"></i> ' . __('Login Failed') . '!</div> ' . __("User or password are incorrect!"));
                    $attempts = $this->auth->loginAttempts($this->request->getPost('username'));
                    $this->view->setVar('attempts', $attempts);
                } else {
                    $this->auth->loginAttempts($this->request->getPost('username'), 'clear');
                    $referer = $this->request->getHTTPReferer();
                    $needBackRedirect = !empty($referer) && strpos(parse_url($referer, PHP_URL_PATH), '/user/signin') !== 0 && parse_url($referer, PHP_URL_HOST) == $this->request->getHttpHost();

                    if ($needBackRedirect) {
                        return $this->response->setHeader("Location", $referer);
                    } else {
                        return $this->response->setHeader("Location", '/');
                    }
                }
            }
        }
    }

    /**
     * Sign up Action
     */
    public function signupAction()
    {
        if ($this->auth->logged_in()) {
            return $this->response->redirect('user/');
        }
        $this->tag->setTitle('Join');
        $this->assets->addJs('js/forms.js');

        // display Recaptcha in form
        /*if ($this->config->recaptcha->enabled == '1') {
            $recap = \Baseapp\Library\Recaptcha::get($this->config->recaptcha->public);
            $this->view->setVar('recap', $recap);
        }*/

        if ($this->request->isPost() == TRUE) {
            $user = new Users();
            $signup = $user->signup();


            if ($signup instanceof Users) {
                $this->flashSession->success(
                    '<i class="close icon"></i>
<div class="header">' . __('Welcome') . '!</div> ' . __("Your account has been setup."));
                return $this->response->redirect("user/");

            } else {
                $this->view->setVar('errors', $signup);
                $this->flashSession->error('<i class="close icon"></i><div class="header">' . __('Error') . '!</div> ' . __("Please correct the errors."));
            }
        }
    }

    /**
     * Log out Action
     */
    public function signoutAction()
    {
        $this->auth->logout();
        $this->response->redirect(NULL);
    }

    /**
     * Activation Action
     */
    public function activationAction()
    {
        $this->view->pick('msg');
        $this->tag->setTitle(__('Activation'));
        $this->view->setVar('title', __('Activation'));

        $params = $this->router->getParams();
        $user = Users::findFirst(array('username=:user:', 'bind' => array('user' => $params[0])));

        if ($user && md5($user->id . $user->email . $user->password . $this->config->auth->hash_key) == $params[1]) {
            $activation = $user->activation();

            if ($activation === NULL) {
                $this->flashSession->notice(
                    '<i class="close icon"></i>
                    <div class="header">' . __('Notice') . '!</div>
                    <div class="content">' . __("Activation has already been completed.") . '</div>');
            } elseif ($activation === TRUE) {
                $this->flashSession->success(
                    '<i class="close icon"></i>
                    <div class="header">' . __('Success') . '!</div>
                    <div class="content">' . __("Activation completed. Please log in.") . '</div>');

                // Redirect to sign in
                $this->view->setVar('redirect', 'user/signin');
            }
        } else {
            $this->flashSession->error(
                '<i class="close icon"></i>
                <div class="header">' . __('Error') . '!</div>
                <div class="content">' . __("Activation cannot be completed. Invalid username or hash.") . '</div>');
        }
    }


    /**
     * Resend activation email
     */
    public
    function resendAction()
    {
        if ($this->auth->logged_in()) { // user must be logged in to use this
            $user_id = $this->auth->get_user()->id;
            $user = Users::findFirstById($user_id);
            $this->view->setVar('user', $user);

            if ($this->request->isPost() == TRUE) {
                if ($this->request->getPost('email')) {
                    $validation = new \Baseapp\Extension\Validation();
                    $validation->add('email', new \Phalcon\Validation\Validator\PresenceOf());
                    $validation->add('email', new \Phalcon\Validation\Validator\Email());
                    $validation->add('email', new \Baseapp\Extension\Uniqueness(array(
                        'model' => '\Baseapp\Models\Users',
                        'message' => 'That email address is already in use!'
                    )));
                    $validation->setLabels(array('email' => 'Email'));
                    $messages = $validation->validate($_POST);
                    if (count($messages)) {
                        $ms = FALSE;
                        foreach ($validation->getMessages() as $msg) {
                            $ms .= $msg->getMessage() . '<br>';
                        }
                        $this->flashSession->error(
                            '<i class="close icon"></i>' .
                            '<div class="ui header">' . __('Error') . '!</div> ' .
                            '<div class="content">' . $ms . '</div>');
                    } else {
                        $email = $this->request->getPost('email', 'email');
                    }
                } else {
                    $email = FALSE;
                }
                if (!ISSET($messages)) {
                    $resend = $user->resend($user_id, $email);
                    if ($resend == TRUE) {
                        $this->flashSession->notice(
                            '<i class="close icon"></i>' .
                            '<div class="ui header">' . __('Check your inbox!') . '!</div> ' .
                            '<div class="content">An activation email has been sent to you.</div>');
                    }
                }
            } else {
                $this->tag->setTitle(__('Activate'));

            }
        } else {
            return $this->notFoundAction();
        }
    }


    /**
     * USER RESET PASSWORD
     * This is the process for a user to reset their password if they have forgotten it.
     * It works in the usual way - user enters email address and if the email address is
     * recognised, the system sends a reset link to their email. The reset link is
     * encrypted and contains a JSON encoded object which has the user id, username,
     * time (link was generated) and a hash key for further verification.
     */
    public function password_resetAction()
    {
        // user clicks link via email so we check them then show change password form
        if ($this->request->getQuery('c')) {
            // decrypt user
            $dec = rawurldecode($this->request->getQuery('c'));
            $decr = $this->getDI()->getShared('crypt')->decrypt($dec);
            $decar = json_decode($decr);

            // get the decrypted user info from url
            $user_id = $decar->user_id;
            $user_name = $decar->username;
            $time = $decar->time;
            $key = $decar->key;

            // check the link has not expired
            if ($time > (time() - 7200)) { // link expires after 2 hours
                // Show the set form
                $user = Users::findFirst(array('id=:user_id: AND username=:username:', 'bind' => array('user_id' => $user_id, 'username' => $user_name)));
                if ($user == TRUE) {
                    $hash = md5($user->id . $user->email . $user->password . $this->config->auth->hash_key);
                    if ($key == $hash) {
                        $this->view->setVar('c', rawurlencode($dec));
                        $this->view->setVar('user', $user->id);
                        $this->view->setVar('username', $user_name);
                    } else {
                        $this->flashSession->error(
                            '<i class="close icon"></i>' .
                            '<div class="ui header">' . __('Error') . '!</div> ' .
                            '<div class="content">Invalid key. You may have used an out of date link.</div>');
                    }
                } else {
                    $this->flashSession->error(
                        '<i class="close icon"></i>' .
                        '<div class="ui header">' . __('Error') . '!</div> ' .
                        '<div class="content">Invalid user.</div>');
                }
            } else {
                $this->flashSession->error(
                    '<i class="close icon"></i>' .
                    '<div class="ui header">' . __('Error') . '!</div> ' .
                    '<div class="content">The password reset link is invalid or has expired.</div>');
            }

            // send the reset email
        } else if ($this->request->isPost() == TRUE) {
            // request reset email submission
            if ($this->request->getPost('action') == 'request_reset') {
                if ($this->security->checkToken()) {
                    $validation = new \Baseapp\Extension\Validation();
                    $validation->add('email', new \Phalcon\Validation\Validator\PresenceOf());
                    $validation->add('email', new \Phalcon\Validation\Validator\Email());
                    $validation->setLabels(array('email' => 'Email'));
                    $messages = $validation->validate($_POST);
                    if (count($messages)) {
                        $this->view->setVar('errors', $validation->getMessages());
                    } else {
                        $email = $this->request->getPost('email', 'email');
                        $email_conf = Users::findFirst(array('email=:email:', 'bind' => array('email' => $email)));
                        if ($email_conf == TRUE) {
                            $send = $email_conf->sendReset($email);
                            if ($send) {
                                $this->flashSession->notice(
                                    '<i class="close icon"></i>' .
                                    '<div class="ui header">' . __('Check your inbox!') . '!</div> ' .
                                    '<div class="content">An email has been sent to you containing a link to reset your password.</div>');
                            }
                        } else {
                            $this->flashSession->error(
                                '<i class="close icon"></i>' .
                                '<div class="ui header">' . __('Error') . '!</div> ' .
                                '<div class="content">We do not have a record of that email address.</div>');
                        }

                    }
                }
                // process and validate the reset form
            } else if ($this->request->getPost('action') == 'change_pass') {
                if ($this->security->checkToken()) {
                    // Reset the password
                    if ($this->request->getPost('c')) {
                        $dec = rawurldecode($this->request->getPost('c'));
                        $decr = $this->getDI()->getShared('crypt')->decrypt($dec);
                        $decar = json_decode($decr);

                        // get the decrypted user info from form
                        $user_id = $decar->user_id;
                        $user_name = $decar->username;
                        $time = $decar->time;
                        $key = $decar->key;
                        if ($time > (time() - 7200)) { // link expires after 2 hours
                            // validate
                            $user = Users::findFirst(array('id=:user_id: AND username=:username:', 'bind' => array('user_id' => $user_id, 'username' => $user_name)));
                            if ($user == TRUE) {
                                $hash = md5($user->id . $user->email . $user->password . $this->config->auth->hash_key);
                                if ($key == $hash) {
                                    $this->view->setVar('username', $user->username);
                                    $this->view->setVar('c', $this->request->getPost('c'));
                                    $validation = new \Baseapp\Extension\Validation();
                                    $validation->add('pass', new \Baseapp\Extension\Password());
                                    $validation->add('pass_conf', new \Phalcon\Validation\Validator\Confirmation(array(
                                        'with' => 'pass',
                                    )));
                                    $validation->setLabels(array('pass' => 'Password', 'pass_conf' => 'Confirm password'));
                                    $messages = $validation->validate($_POST);
                                    if (count($messages)) {
                                        $this->view->setVar('errors', $validation->getMessages());
                                    } else {
                                        // update user's password
                                        $new_pass = $this->auth->hashPass($this->request->getPost('pass'));
                                        $user->password = $new_pass;
                                        if ($user->update() == TRUE) {
                                            $this->flashSession->success(
                                                '<i class="close icon"></i>' .
                                                '<div class="ui header">' . __('Password updated') . '!</div> ' .
                                                '<div class="content">Your password has been successfully updated. Please login.</div>');
                                            $this->view->setVar('completed', '1');
                                            $this->response->redirect('user/signin');
                                        } else {
                                            echo 'Update failed';
                                            exit;
                                        }
                                    }
                                } else {
                                    $this->flashSession->error(
                                        '<i class="close icon"></i>' .
                                        '<div class="ui header">' . __('Error') . '!</div> ' .
                                        '<div class="content">Invalid key.</div>');
                                }
                            } else {
                                $this->flashSession->error(
                                    '<i class="close icon"></i>' .
                                    '<div class="ui header">' . __('Error') . '!</div> ' .
                                    '<div class="content">Invalid user.</div>');
                            }
                        } else {
                            $this->flashSession->error(
                                '<i class="close icon"></i>' .
                                '<div class="ui header">' . __('Error') . '!</div> ' .
                                '<div class="content">The password reset link is invalid or has expired.</div>');
                        }

                    }
                } else {
                    // CSRF check failed
                    $this->flashSession->warning(
                        '<i class="close icon"></i>' .
                        '<div class="ui header">Form expired!</div> ' .
                        '<div class="content">The form has expired. Please try again.</div>');
                }
            }
        } else {
            // show the initial form where user enters email
        }
    }

}

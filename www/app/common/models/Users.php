<?php

namespace Baseapp\Models;

use Baseapp\Library\Auth;
use Baseapp\Library\Email;

/**
 * User Model
 *
 * @category    Model
 */
class Users extends \Phalcon\Mvc\Model
{

    public $request;


    /**
     * User initialize
     */
    public function initialize()
    {
        $this->hasMany('id', __NAMESPACE__ . '\Tokens', 'user_id', array(
            'alias' => 'Tokens',
            'foreignKey' => array(
                'action' => \Phalcon\Mvc\Model\Relation::ACTION_CASCADE
            )
        ));
        $this->hasMany('id', __NAMESPACE__ . '\RolesUsers', 'user_id', array(
            'alias' => 'Roles',
            'foreignKey' => array(
                'action' => \Phalcon\Mvc\Model\Relation::ACTION_CASCADE
            )
        ));

        $this->request = $this->getDI()->getShared('request');
    }




    /**
     * Activation User method
     *
     */
    public function activation()
    {
        $ext = $this->getUserRoles($this->id);
        if($ext){
            // check existing roles
            if($ext[0]['name'] == 'unconfirmed'){
                // process the user role update
                if($this->setUserRole($this->id, 'registered')){
                    return TRUE;
                }

            }else{
                // user already seems to be confirmed
                return NULL;
            }
        }

    }


    /**
     * Set a user role
     *
     * @param $user_id
     * @param $role
     * @return bool
     */
    public function setUserRole($user_id, $role){
        if(is_int($role)){
            // we are passing in a role id
            $role_id = $role;
            $find_role = Roles::findFirst(array("id='$role_id'"));
        }else if(is_string($role)){
            // we are passing in a role name
            $role_name = $role;
            $find_role = Roles::findFirst(array("name='$role_name'"));
        }
        // first check if user has any existing roles
        $existing = self::getUserRoles($user_id);
        if($existing){
            // user has existing roles so we check if we need to replace or add new role
            if($existing[0]['name'] == 'unconfirmed'){
                if($find_role->name == 'registered'){
                    // delete the unconfirmed role before adding the new role
                    $delRole = RolesUsers::find(array('conditions' => "role_id = '1' AND user_id = '$user_id'"));
                    $delRole->delete();
                }
            }
        }

        // Add login role
        $new_role = new RolesUsers();
        $new_role->user_id = $user_id;
        $new_role->role_id = $find_role->id;


        if ($new_role->create() === true) {
            return TRUE;
        } else {
            \Baseapp\Bootstrap::log($this->getMessages());
            return $this->getMessages();
        }
    }


    /**
     * Get a list of roles a user has
     * Or check if user has specific role
     * by adding the role id or name
     *
     * @param $user_id
     * @return array
     */
    public static function getUserRoles($user_id, $role=FALSE){
        // search if user has a specific role
        if($role){
            if(is_int($role)){ // searching by role if
                $search = RolesUsers::find(array('conditions' => "user_id = '$user_id'' AND role_id = '$role'"));
                if($search){
                    return TRUE;
                }
            }else{ // search by role name
                $type = Roles::findFirstByName($role);
                //print_r($type);exit;
                if(count($type) > 0) {
                    $search = RolesUsers::find(array('conditions' => "user_id = '$user_id' AND role_id = '$type->id'"));
                    //print_r($type->id);exit;
                    if(count($search) > 0){
                        return TRUE;
                    }
                }
            }
        }else { // get all user roles as an array
            $roles = RolesUsers::find(array('conditions' => "user_id = $user_id"));
            if ($roles) {
                $ro = array();
                foreach ($roles as $role) {
                    $r = Roles::findFirst(array("id = $role->role_id"));
                    $ro[] = array('id' => $role->role_id, 'name' => $r->name);
                }
                return $ro;
            }
        }
    }




    /**
     * Get user's role relation
     *
     *
     * @param string $role role to get one RolesUsers
     */
    public function getRole($role = 'registered')
    {
        $role = Roles::findFirst(array('name=:role:', 'bind' => array(':role' => $role)));
        // Return null if role does not exist
        if (!$role) {
            return null;
        }
        // Return the role if user has the role otherwise false
        return $this->getRoles(array('role_id=:role:', 'bind' => array(':role' => $role->id)))->getFirst();
    }



    /**
     * Sign up User method
     *
     */
    public function signup()
    {

        $validation = new \Baseapp\Extension\Validation();

        $validation->add('username', new \Phalcon\Validation\Validator\PresenceOf());
        $validation->add('username', new \Baseapp\Extension\Uniqueness(array(
            'model' => '\Baseapp\Models\Users',
        )));
        $validation->add('username', new \Phalcon\Validation\Validator\StringLength(array(
            'min' => 4,
            'max' => 24,
        )));
        $validation->add('username', new \Phalcon\Validation\Validator\Regex(array(
            'pattern' => '/^[[:alnum:]_]+( [[:alnum:]]+)*$/', // only allow letters, numbers, underscore and single space between words
            'message' => 'User name must only contain letters, numbers and space'
        )));
        $validation->add('password', new \Baseapp\Extension\Password());
        $validation->add('repeatPassword', new \Phalcon\Validation\Validator\Confirmation(array(
            'with' => 'password',
        )));
        $validation->add('email', new \Phalcon\Validation\Validator\PresenceOf());
        $validation->add('email', new \Phalcon\Validation\Validator\Email());
        $validation->add('email', new \Baseapp\Extension\Uniqueness(array(
            'model' => '\Baseapp\Models\Users',
        )));
        $validation->add('agree', new \Phalcon\Validation\Validator\Identical(array(
            'value' => 'yes',
            'message' => 'You must accept the terms and conditions'
        )));
        // Recaptcha validation
        if($this->getDI()->getShared('config')->recaptcha->enabled == '1') {
            $validation->add('g-recaptcha-response', new \Baseapp\Extension\Recaptcha());
        }

        $validation->setLabels(array('username' => __('Username'), 'password' => __('Password'), 'repeatPassword' => __('Repeat password'), 'email' => __('Email'), 'repeatEmail' => __('Repeat email')));
        $messages = $validation->validate($_POST);



        // Check for validation messages or Recaptcha error
        if (count($messages)) {
            return $validation->getMessages();
        } else {
            $this->username = $this->request->getPost('username');
            $this->password = $this->getDI()->getShared('auth')->hashPass($this->request->getPost('password'));
            $this->email = $this->request->getPost('email');
            $this->logins = 0;

            if ($this->create() === true) {
                $hash = md5($this->id . $this->email . $this->password . $this->getDI()->getShared('config')->auth->hash_key);
                $this->setUserRole($this->id, 'unconfirmed');
                // log new user in
                $login = $this->getDI()->getShared('auth')->login($this->request->getPost('username'), $this->request->getPost('password'), FALSE);
                $email = new Email();
                $email->prepare(__('Activation'), $this->request->getPost('email'), 'activation', array('username' => $this->request->getPost('username'), 'hash' => $hash));

                if ($email->Send() === true) {
                    unset($_POST);
                    return $this;
                } else {
                    \Baseapp\Bootstrap::log($email->ErrorInfo);
                    return false;
                }

            } else {
                \Baseapp\Bootstrap::log($this->getMessages());
                return false;
            }
        }
    }


    /**
     * User edit password
     *
     * @param $password
     * @return bool
     */
    public function editPassword($user_id, $password){
        $user = $this::findFirst(array('conditions' => "id = $user_id"));
        $user->password = $this->getDI()->getShared('auth')->hashPass($password);
        if($user->update() === TRUE){
            return TRUE;
        }
    }


    /**
     * Send password reset
     * @param $email
     * @return bool
     * @throws \Exception
     * @throws \phpmailerException
     */
    public function sendReset($email)
    {
        $user = $this::findFirst(array('email=:email:', 'bind' => array('email' => $email)));
        if ($user == TRUE) {
            $hash = md5($user->id . $email . $user->password . $this->getDI()->getShared('config')->auth->hash_key);

            $cryptLine = json_encode(array('user_id' => $user->id, 'username' => $user->username, 'time' => time(), 'key' => $hash));
            $encrypted = $this->getDI()->getShared('crypt')->encrypt($cryptLine);
            //echo $encrypted;exit;
            $send = new Email();
            $send->prepare(__('Password'), $email, 'reset', array('username' => $user->username, 'encrypted' => rawurlencode($encrypted), 'hash' => $hash));
            if ($send->Send() === TRUE) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * Resend activation email
     *
     * @param $user
     * @param bool|FALSE $email_address
     * @return bool
     * @throws \Exception
     * @throws \phpmailerException
     */
    public function resend($user, $email_address = FALSE)
    {
        $re_user = self::findFirst(array('conditions' => "id = '$user'"));
        $isAlreadyUnconfirmed = $this::getUserRoles($user, 'unconfirmed');
        if( ! $isAlreadyUnconfirmed){
            $this::setUserRole($user, 'unconfirmed');
        }
        if ($email_address) {
            $send_to = $email_address;
            // if user changed email address update user email address in database
            $re_user->email = $email_address;
            if ($re_user->update() !== TRUE) {
                \Baseapp\Bootstrap::log($this->getMessages());
                return $this->getMessages();
            }
        } else {
            $send_to = $re_user->email;
        }
        $hash = md5($user . $send_to . $re_user->password . $this->getDI()->getShared('config')->auth->hash_key);

        $email = new Email();
        $email->prepare(__('Activation'), $send_to, 'activation', array('username' => $re_user->username, 'hash' => $hash));
        if ($email->Send() === TRUE) {
            return TRUE;
        }
    }


}

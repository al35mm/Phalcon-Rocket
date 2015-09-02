<?php

namespace Baseapp\Library;

use Baseapp\Models\Users;
use Baseapp\Models\UserSessions as UserSessions;
use Baseapp\Models\Tokens;
use Baseapp\Models\FailedLogins;

/**
 * Auth Library
 */
class Auth
{

    private $_config = array();
    private static $_instance;
    private $_cookies;
    private $_session;

    /**
     * Singleton pattern
     *
     * @return Auth instance
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Auth;
        }

        return self::$_instance;
    }

    /**
     * Private constructor - disallow to create a new object
     */
    private function __construct()
    {
        // Overwrite _config from config.ini
        if ($_config = \Phalcon\DI::getDefault()->getShared('config')->auth) {
            foreach ($_config as $key => $value) {
                $this->_config[$key] = $value;
            }
        }

        $this->_cookies = \Phalcon\DI::getDefault()->getShared('cookies');
        $this->_session = \Phalcon\DI::getDefault()->getShared('session');
        $this->_security = \Phalcon\DI::getDefault()->getShared('security');
    }

    /**
     * Private clone - disallow to clone the object
     */
    private function __clone()
    {

    }

    /**
     * Logs a user in, based on the authautologin cookie.
     *
     * @return mixed
     */
    private function auto_login()
    {
        if ($this->_cookies->has('authautologin')) {
            $cookieToken = $this->_cookies->get('authautologin')->getValue();

            // Load the token
            $token = Tokens::findFirst(array('token=:token:', 'bind' => array(':token' => $cookieToken)));

            // If the token exists
            if ($token) {
                // Load the user and his roles
                $user = $token->getUser();
                $roles = $this->get_roles($user);

                // If user has login role and tokens match, perform a login
                if (isset($roles['registered']) && $token->user_agent === sha1(\Phalcon\DI::getDefault()->getShared('request')->getUserAgent())) {
                    // Save the token to create a new unique token
                    $token->token = $this->create_token();
                    $token->save();

                    // Set the new token
                    $this->_cookies->set('authautologin', $token->token, $token->expires);

                    // Finish the login
                    $this->complete_login($user);

                    // Regenerate session_id
                    session_regenerate_id();

                    // Store user in session
                    $this->_session->set($this->_config['session_key'], $user);
                    // Store user's roles in session
                    if ($this->_config['session_roles']) {
                        $this->_session->set($this->_config['session_roles'], $roles);
                    }

                    // Automatic login was successful
                    return $user;
                }

                // Token is invalid
                $token->delete();
            } else {
                $this->_cookies->set('authautologin', "", time() - 3600);
                $this->_cookies->delete('authautologin');
            }
        }

        return false;
    }

    /**
     * Complete the login for a user by incrementing the logins and saving login timestamp
     *
     * @param object $user user from the model
     *
     * @return void
     */
    private function complete_login(Users $user)
    {
        // Update the number of logins
        $user->logins = $user->logins + 1;

        // Set the last login date
        $user->last_login = time();

        // Save the user
        $user->update();
    }

    /**
     * Create auto login token.
     *
     * @return  string
     */
    protected function create_token()
    {
        do {
            $token = sha1(uniqid(\Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 32), true));
        } while (Tokens::findFirst(array('token=:token:', 'bind' => array(':token' => $token))));

        return $token;
    }

    /**
     * Gets the roles of user.
     *
     * @param object $user user from the model
     *
     * @return array
     */
    public function get_roles($user)
    {
        $roles = array();

        if ($user instanceof Users) {
            // Find related records for a particular user
            foreach ($user->getRoles() as $roleuser) {
                // Get related role
                $role = $roleuser->getRole()->toArray();
                $roles [$role['name']] = $role['id'];
            }
        }

        return $roles;
    }


    /**
     * Get a list of all roles a user has.
     * This works better than the above
     * for getting a quick list!
     *
     * @param $user_id
     * @return array
     */
    public function get_user_roles($user_id, $role=FALSE){
        $roles = Users::getUserRoles($user_id, $role);
        return $roles;
    }



    /**
     * Gets the currently logged in user from the session.
     * Returns null if no user is currently logged in.
     *
     * @return mixed
     */
    public function get_user()
    {

        $user = $this->_session->get($this->_config['session_key']);

        // Check for "remembered" login
        if (!$user) {
            $user = $this->auto_login();
        }
        // refresh user session regularly to mitigate session fixation attacks

            if( ! $this->refresh_session()){
                return FALSE;
            }

        return $user;
    }



    /**
     * Perform a password hash using PHP password_hash
     * Note: this is identical to Phalcon's hash
     *
     * @param string $str string to hash
     * @return string
     */
    public function hashPass($str)
    {
        // Phalcon's hashing system
        //return $this->_security->hash($str);

        // Using PHP5.5's built in system
        return password_hash($str, PASSWORD_DEFAULT, ['cost' => \Phalcon\DI::getDefault()->getShared('config')->auth->hash_workload]);
    }


    /**
     * Check password hash
     *
     * @param $str
     * @param $dbPass
     * @return mixed
     */
    public function checkPass($str, $hash){
        // Phalcon's checkHash
        //return $this->_security->checkHash($str, $hash);

        // Using PHP5.5's built in system
        return password_verify($str, $hash);
    }


    /**
     * Password strength test
     * Use on sign up process
     * to check password is
     * secure enough
     * @TODO I think this is now deprecated as password checking has been moved to password extension - nrrd to check before removing this
     *
     * @param $str
     * @return bool
     */
    public function passStrength($str){
        $error = FALSE;
        // make sure it is long enough
        if(strlen($str) < 8){
            $error['length'] = 'must be 8 characters minimum.';
        }
        // must contain letters & numbers and must have 1 upper case letter!
        if( ! preg_match('/[A-Za-z]/', $str) && preg_match('/[0-9]/', $str)){
            $error['alphanum'] = 'Must be alphanumeric and contain capitals and lower case.';
        }
        return $error;
    }




    /**
     * Checks if a session is active.
     *
     * @param mixed $role role name
     *
     * @return boolean
     */
    public function logged_in($role = null)
    {
        // Get the user from the session
        $user = $this->get_user();
        if (!$user) {
            return false;
        }

        // If user exists in session
        if ($user) {

            // check session in database
            $sdb = UserSessions::findFirstBySessionId($this->_session->getId());
            if($sdb){
                $thisAgent = \Phalcon\DI::getDefault()->getShared('request')->getUserAgent();
                if($sdb->user_agent != $thisAgent || $sdb->user_id != $user->id){
                    // user agent or user IDs do not match
                    return FALSE;
                }
            }else{
                // Session not found in DB
                return FALSE;
            }

            // refresh the user session if needs be
            if( ! $this->refresh_session()){
                return FALSE;
            }

            // If we don't have a roll no further checking is needed
            if (!$role) {
                return true;
            }

            // Check if user has the role
            if ($this->_config['session_roles'] && $this->_session->has($this->_config['session_roles'])) {
                // Check in session
                $roles = $this->_session->get($this->_config['session_roles']);
                $role = isset($roles[$role]) ? $roles[$role] : null;
            } else {
                // Check in db
                $role = $user->hasRole($role);
            }



            // Return true if user has role
            return $role ? true : false;
        }
    }





    /**
     * Attempt to log in a user by using an ORM object and plain-text password.
     *
     * @param string $user user to log in
     * @param string $password password to check against
     * @param boolean $remember enable autologin
     * @return boolean
     */
    public function login($user, $password, $remember = false)
    {
        if (! $user instanceof Users) {
            $username = $user;

            // Username not specified
            if (!$username) {
                return null;
            }

            // Load the user
            $user = Users::findFirst(array('username=:username:', 'bind' => array(':username' => $username)));
        }

        if ($user) {
            $roles = $this->get_roles($user);


            // check the password
            if (is_string($password)) {
                $checkPassword = $this->checkPass($password, $user->password);
            }

            // If user have login role and the passwords match, perform a login
            if ((isset($roles['registered']) || isset($roles['unconfirmed'])) && $checkPassword === TRUE) {
                // start the session
                if($this->startSession($user, $roles, $remember)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            } else {
                // Login failed
                return false;
            }
        }
        // No user found
        return null;
    }


    /**
     * Start user session
     *
     * @param $user
     * @param $roles
     * @param $remember
     * @return bool
     */
    private function startSession($user, $roles, $remember){
        if ($remember === true) {
            // Create a new autologin token
            $token = new Tokens();
            $token->user_id = $user->id;
            $token->user_agent = sha1(\Phalcon\DI::getDefault()->getShared('request')->getUserAgent());
            $token->token = $this->create_token();
            $token->created = time();
            $token->expires = time() + $this->_config['lifetime'];

            if ($token->create() === true) {
                // Set the autologin cookie
                $this->_cookies->set('authautologin', $token->token, time() + $this->_config['lifetime']);
            }
        }

        // Finish the login
        $this->complete_login($user);

        // Regenerate session_id
        session_regenerate_id();

        // Store user in session
        $this->_session->set($this->_config['session_key'], $user);
        $this->_session->set('time', time());
        // Store user's roles in session
        if ($this->_config['session_roles']) {
            $this->_session->set($this->_config['session_roles'], $roles);
        }
        // update DB
        $this->sessionDb($user->id);
        return true;
    }



    /**
     * Refresh user data stored in the session from the database.
     * Returns null if no user is currently logged in.
     *
     * @return mixed
     */
    public function refresh_session()
    {
        $user = $this->_session->get($this->_config['session_key']);

            if (!$user) {
                return null;
            } else {
                if($this->_session->get('time') <= time() - \Phalcon\DI::getDefault()->getShared('config')->session->options['lifetime']) {
                    // Get user's data from db
                    $user = Users::findFirstById($user->id);
                    $roles = $this->get_roles($user);

                    // Regenerate session_id
                    session_regenerate_id(TRUE);

                    if( ! session_id()){
                        if($this->_session->isStarted()) {
                            $this->_session->destroy();
                        }
                        $this->_session->start();
                        session_regenerate_id(TRUE);
                    }

                    // Store user in session
                    $this->_session->set($this->_config['session_key'], $user);
                    $this->_session->set('time', time());
                    // Store user's roles in session
                    if ($this->_config['session_roles']) {
                        $this->_session->set($this->_config['session_roles'], $roles);
                    }
                    // add session to DB
                    $this->sessionDb($user->id);
                }

                session_write_close();
                return $user;
            }

    }


    /**
     * Record session in DB
     *
     * @param $user_id
     * @return bool
     */
    private function sessionDb($user_id, $delete = FALSE){
        if(session_id() && $user_id) {
            if($delete){
                // destroy the session in the DB
                $del = UserSessions::find(array('conditions' => "session_id = :sessId:", 'bind' => array(':sessId' => $this->_session->getId())));
                if(count($del) > 0){
                    $del->delete();
                }
            }else {
                $userAgent = \Phalcon\DI::getDefault()->getShared('request')->getUserAgent();
                $ip = \Phalcon\DI::getDefault()->getShared('request')->getClientAddress();
                // purge all old sessions
                $purge_age = time() - 60 * 60 * 24;
                $sesId = $this->_session->getId();
                $del = UserSessions::find(array('(conditions' => "time < '$purge_age') OR (user_id = $user_id AND user_agent = ':userAgent:')", 'bind' => array(':userAgent' => $userAgent)));
                $del->delete();
                // create new record
                $user_session = new UserSessions();
                $user_session->user_id = $user_id;
                $user_session->session_id = $this->_session->getId();
                $user_session->user_agent = $userAgent;
                $user_session->ip = $ip;
                $user_session->time = time();
                if ($user_session->save()) {

                    return TRUE;
                }
            }
        }
    }


    /**
     * Just a handy little function
     * to get the user's session time
     * mostly here for testing
     *
     * @return mixed
     */
    public function getSessionTime(){
        return $this->_session->get('time');
    }


    /**
     * Log out a user by removing the related session variables
     * Remove any autologin cookies.
     *
     * @param boolean $destroy completely destroy the session
     * @param boolean $logoutAll remove all tokens for user
     * @return boolean
     */
    public function logout($destroy = false, $logoutAll = false)
    {
        if ($this->_cookies->has('authautologin')) {
            $cookieToken = $this->_cookies->get('authautologin')->getValue();

            // Delete the autologin cookie to prevent re-login
            $this->_cookies->set('authautologin', "", time() - 3600);
            $this->_cookies->delete('authautologin');

            // Clear the autologin token from the database
            $token = Tokens::findFirst(array('token=:token:', 'bind' => array(':token' => $cookieToken)));

            if ($logoutAll) {
                // Delete all user tokens
                foreach (Tokens::find(array('user_id=:user_id:', 'bind' => array(':user_id' => $token->user_id))) as $_token) {
                    $_token->delete();
                }
            } else {
                if ($token) {
                    $token->delete();
                }
            }
        }
        // delete session from DB
        $this->sessionDb($this->_session->get($this->_config['session_key'])->id, TRUE);
        // Destroy the session completely
        if ($destroy === true) {
            $this->_session->destroy();
        } else {
            // Remove the user from the session
            $this->_session->remove($this->_config['session_key']);
            // Remove user's roles from the session
            if ($this->_config['session_roles']) {
                $this->_session->remove($this->_config['session_roles']);
            }

            // Regenerate session_id
            session_regenerate_id();
        }

        // Double check
        return !$this->logged_in();
    }




    /**
     * LOGIN BRUTE FORCE PROTECTION
     * This will slow down failed login attempts by throttling the request time.
     * If the requests continue to fail a Recaptcha will be shown (if enabled)
     * and finally the login form will be disabled for a while.
     *
     * @param $userName
     * @param bool $clear
     * @return array
     */
    public function loginAttempts($userName, $option=FALSE){

        $blockTime = \Phalcon\DI::getDefault()->getShared('config')->brute->block_time * 60;

        $user = Users::findFirst(array('username=:username:', 'bind' => array(':username' => $userName)));
        if($user){
            $userId = $user->id;
        }else{
            // user didn't even get the user name right
            $userId = NULL;
        }

        $footprint = md5(\Phalcon\DI::getDefault()->getShared('request')->getClientAddress() . \Phalcon\DI::getDefault()->getShared('request')->getUserAgent());
        // if clearing attempts after successful login
        if($option == 'clear'){
            $toDelete = FailedLogins::find(array('conditions' => 'user_id=:user_id: OR time <' . (time() - 60 * 60 * 24* 365), 'bind' => array(':user_id' => $userId)));
            if(count($toDelete) > 0){
                $toDelete->delete();
                return TRUE;
            }
        }
        $captcha = FALSE;
        $block = FALSE;
        // get attempts so far
        if(is_numeric($userId)) {
            // if username was correct
            $attemptsDb = FailedLogins::find(array('conditions' => '(user_id=:user_id: OR footprint=:footprint:) AND time >' . (time() - $blockTime), 'bind' => array(':user_id' => $userId, ':footprint' => $footprint)));
            $attempts = count($attemptsDb);
        }else{
            // if username was not correct - we search on footprint only
            $attemptsDb = FailedLogins::find(array('conditions' => 'footprint=:footprint: AND time >' . (time() - $blockTime), 'bind' => array(':footprint' => $footprint)));
            $attempts = count($attemptsDb);
        }


        if($option != 'state') {
            // increment the number of attempts
            $attempts++;
            // generate record in DB
            $fail = new FailedLogins();
            $fail->user_id = $userId;
            $fail->time = time();
            $fail->ip = \Phalcon\DI::getDefault()->getShared('request')->getClientAddress();
            $fail->useragent = \Phalcon\DI::getDefault()->getShared('request')->getUserAgent();
            $fail->footprint = $footprint;
            $fail->save();
        }

        // carry out blocks
        if($attempts >= 1 && $attempts <= 3){
            if($option != 'state') {
                sleep($attempts * 2); // throttle speed to slow them down
            }
        }else if($attempts > 3 && $attempts < 10){
            if($option != 'state') {
                sleep($attempts); // throttle attempts for longer and longer
            }
            $captcha = TRUE; // now we start using captcha
        }else if($attempts >= 10){
            if($option != 'state') {
                sleep($attempts); // throttle attempts for longer and longer
            }
            $captcha = TRUE; // now we start using captcha
            $block = TRUE; // block the login form
        }
        return array('attempts' => $attempts, 'attempts_left' => (10 - $attempts), 'captcha' => $captcha, 'block' => $block);
    }



}

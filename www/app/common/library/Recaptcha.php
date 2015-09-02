<?php
/**
 SUPPORT FOR RECAPTCHA 2
 */

namespace Baseapp\Library;

/**
 * Class Recaptcha
 * This is the Recaptcha class
 * See config.ini to enable/disable and set API keys and ptions
 *
 * @package Baseapp\Library
 */

class Recaptcha extends \Phalcon\DI\Injectable {

    const RECAPTCHA_VERIFY_SERVER = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Setup reCAPTCHA error messages
     */
    const RECAPTCHA_ERROR_KEY = 'To use reCAPTCHA you must get an API key from <a href="https://www.google.com/recaptcha/admin/create">https://www.google.com/recaptcha/admin/create</a>';
    const RECAPTCHA_ERROR_REMOTE_IP = 'For security reasons, you must pass the remote IP address to reCAPTCHA';




    /**
     * Get Recaptcha
     *
     * @param $publicKey
     * @param string $error
     * @return string
     */
    public static function recaptcha(){
        // Merging method arguments with class fields
        $publicKey = \Phalcon\DI::getDefault()->getShared('config')->recaptcha->public or die(self::RECAPTCHA_ERROR_KEY);

        return '<div class="g-recaptcha" data-sitekey="' . $publicKey . '" data-theme="' . \Phalcon\DI::getDefault()->getShared('config')->recaptcha->theme . '"></div>';

    }


    /**
     * Check Recaptcha
     *
     * @param $privateKey
     * @param $remoteIP
     * @param $response
     * @return array|bool
     */
    public static function check($remoteIP, $response){

        $privateKey = \Phalcon\DI::getDefault()->getShared('config')->recaptcha->private or die(self::RECAPTCHA_ERROR_KEY);
        $result = array();
        $result['success'] = FALSE;
        $result['error-codes'] = FALSE;
        $result = json_decode(file_get_contents(self::RECAPTCHA_VERIFY_SERVER . "?secret=$privateKey&response=".$response."&remoteip=".$remoteIP), true);
       if($result['success'] == FALSE){

           // errors
           if(is_array($result['error-codes'])){
               if($result['error-codes'][0] == 'missing-input-response'){
                   return array('success' => FALSE, 'error' => __('Please verify you are human'));
               }else if($result['error-codes'][0] == 'invalid-input-response'){
                   return array('success' => FALSE, 'error' => __('You have failed the human verification test!'));
               }
           }else{
               return array('success' => FALSE, 'error' => __('There was an unknown error!'));
           }
       }else{
           return array('success' => TRUE, 'error' => FALSE);
       }
        return FALSE;
    }


}
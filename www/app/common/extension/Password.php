<?php
/**
 * This class validates the strength of a password
 * by doing a number of tests on it and giving the
 * submitted password a score out of a maximum of 10.
 *
 * You can set the minimum score in config.ini [auth]
 */

namespace Baseapp\Extension;

/**
 * Class Password
 * Password validation to ensure password is strong enough as set in config.ini
 *
 * @package Baseapp\Extension
 */

class Password extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface {

    public function validate(\Phalcon\Validation $validation, $field){
        $password = $validation->getValue($field);

        $error=FALSE;

        if ( strlen( $password ) == 0 )
        {
            $error = 'You must provide a password.';
        }

        $strength = 0;

        /*** get the length of the password ***/
        $length = strlen($password);

        /*** check if password is not all lower case ***/
        if(strtolower($password) != $password)
        {
            $strength += 1;
        }

        /*** check if password is not all upper case ***/
        if(strtoupper($password) != $password)
        {
            $strength += 1;
        }

        /*** check string length is 8 -15 chars ***/
        if($length >= 8 && $length <= 15)
        {
            $strength += 1;
        }

        /*** check if lenth is 16 - 35 chars ***/
        if($length >= 16 && $length <=35)
        {
            $strength += 2;
        }

        /*** check if length greater than 35 chars ***/
        if($length > 35)
        {
            $strength += 3;
        }

        /*** get the numbers in the password ***/
        preg_match_all('/[0-9]/', $password, $numbers);
        $strength += count($numbers[0]);

        /*** check for special chars ***/
        preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialchars);
        $strength += sizeof($specialchars[0]);

        /*** get the number of unique chars ***/
        $chars = str_split($password);
        $num_unique_chars = sizeof( array_unique($chars) );
        $strength += $num_unique_chars * 2;

        /*** strength is a number 1-10; ***/
        $strength = $strength > 99 ? 99 : $strength;
        $strength = floor($strength / 10 + 1);

        $min_strength = \Phalcon\DI::getDefault()->getShared('config')->auth->password_strength;
        if($error || $strength < $min_strength) {
            $message = $this->getOption("message");
            if (empty($message)) {
                if($error){
                    $message = 'Please enter a passsword!';
                }else{
                    $message = 'Weak password! Try longer with mix of upper & lower case and numbers';
                }
            }
            $validation->appendMessage(new \Phalcon\Validation\Message($message, $field, "Password"));
            return false;
        }
        return TRUE;
    }

}
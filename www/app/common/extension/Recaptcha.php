<?php
/**
 * Recaptcha.php
 * Project: Phalcon Rocket App
 * Developer: Al
 * Created: 31/08/2015 10:24
 * Using: PhpStorm
 */

namespace Baseapp\Extension;


class Recaptcha extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{

    public function validate(\Phalcon\Validation $validation, $field) {
        $response = $validation->getValue($field);

        $answer = \Baseapp\Library\Recaptcha::check(
            \Phalcon\DI::getDefault()->getShared('request')->getClientAddress(),
            $response
        );
        if ($answer['error'] == TRUE) {
            // Captcha is incorrect
            $recap_error = $answer['error'];
            $validation->appendMessage(new \Phalcon\Validation\Message($recap_error, $field, "Recaptcha"));
            return false;
        }
        return TRUE;
    }

}
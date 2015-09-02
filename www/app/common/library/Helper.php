<?php
/**
 * Created by PhpStorm.
 * User: Al
 * Date: 01/08/2015
 * Time: 20:52
 */

namespace Baseapp\Library;

use \Phalcon\Tag as Tag;
use Baseapp\Library\Tool as Tool;

/**
 * Class Helper
 * This class contains some general helper functions
 * that can be used in a project.
 *
 * @package Baseapp\Library
 */

class Helper
{


    /**
     * returns short formatted MySQL version
     *
     * @return string
     */
    public static function mysqlVersion()
    {
        if (self::isEnabled('shell_exec')) {
            $raw = explode(',', shell_exec('mysql -V'));
            $raw = explode('Distrib', $raw[0]);
            return trim($raw[1]);
        } else {
            return 'Unknown';
        }
    }


    /**
     * Get the number only of php version
     * @return mixed
     */
    public static function php_version()
    {
        $ve = phpversion();
        $ve = explode('-', $ve);
        return $ve[0];
    }


    /**
     * Check if a PHP function is callable
     * @param $func
     * @return bool
     */
    private static function isEnabled($func)
    {
        return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);
    }


    /**
     * Dynamically generate form fields with Semantic Ui classes
     * and error messages.
     * @TODO This is work in progress
     *
     * @param $type
     * @param $name
     * @param bool|FALSE $value
     * @param bool|FALSE $extras
     * @param bool|FALSE $error
     * @return string
     */
    public static function formField($type, $name, $value = FALSE, $extras = FALSE, $error = FALSE)
    {
        $tag = Tag;
        switch ($type) {
            case 'text':
                return '<label for="' . $name . '">' . Tool::label($name) . '</label>
                <div class="ui field' . $error == TRUE ? ' error' : '' . '">
                ' .
                $tag->textField(array($name, 'value' => $value, $extras)) .
                $error == TRUE ? '<span class="ui red pointing above label">' . $error . '</span>' : ''
                    . '
                </div>';
                break;
            case 'password':

                break;
            case 'textarea';

                break;
            case 'checkbox';

                break;
        }
    }

}
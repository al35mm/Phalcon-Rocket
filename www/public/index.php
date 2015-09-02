<?php

/**
 * index.php
 */
error_reporting(E_ALL);



try {
    // Global translation function
    if (!function_exists('__')) {

        /**
         * Translate message
         *
         * @param string $string string to translate
         * @param array $values replace substrings
         *
         * @return string translated string
         */
        function __($string, array $values = NULL)
        {
            return \Baseapp\Library\I18n::instance()->_($string, $values);
        }

    }

    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__DIR__));
    }

    if (!defined('APP_PATH')) {
        define('APP_PATH', dirname(__DIR__));
    }

    require_once APP_PATH . '/app/Bootstrap.php';

    $app = new \Baseapp\Bootstrap(new \Phalcon\DI\FactoryDefault());
    echo $app->handle()->getContent();
} catch (\Phalcon\Exception $e) {
    \Baseapp\Bootstrap::exception($e);
} catch (\PDOException $e) {
    \Baseapp\Bootstrap::exception($e);
} catch (\Exception $e) {
    \Baseapp\Bootstrap::exception($e);
}


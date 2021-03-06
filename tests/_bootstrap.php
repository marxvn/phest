<?php

require __DIR__.'/../vendor/autoload.php';

$loader = new \Phalcon\Loader();
$loader->registerNamespaces([
    'Ovide\Libs\Mvc\Rest'               => __DIR__.'/../Libs/Mvc/Rest/',
    'Ovide\Libs\Mvc\Rest\Exception'     => __DIR__.'/../Libs/Mvc/Rest/Exception/',
    'Ovide\Libs\Mvc\Rest\HeaderHandler' => __DIR__.'/../Libs/Mvc/Rest/HeaderHandler/',
    'Mocks\Controllers'                 => __DIR__.'/Mocks/Controllers/',
    'Mocks\Headers'                     => __DIR__.'/Mocks/Headers/',
    'Mocks\Examples'                    => __DIR__.'/Mocks/Examples/',
    'Mocks\Middlewares'                 => __DIR__.'/Mocks/Middlewares/',
]);
$loader->register();

set_error_handler(function($errNumber, $errStr, $errFile, $errLine) {
    throw new \ErrorException($errStr, 0, $errNumber, $errFile, $errLine);
});

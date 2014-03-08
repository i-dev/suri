<?php
$loader = require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['pathroot'] = __DIR__;

require_once __DIR__.'/app/bootstrap.php';

$app['debug'] = false;


include __DIR__.'/src/Idev/Controller/controllers_backend.php';
include __DIR__.'/src/Idev/Controller/controllers_frontend.php';

$app->run();
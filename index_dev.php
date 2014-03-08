<?php
$loader = require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();
$app['pathroot'] = __DIR__;

require_once __DIR__.'/app/bootstrap.php';

$app['debug'] = false;

$app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/web/cache/profiler',
        'profiler.mount_prefix' => '/_profiler', 
));

$app['swiftmailer.transport'] = $app->extend('swiftmailer.transport', function ($transport, $app) {
    $transport = new \Swift_Transport_NullTransport(
            $app['swiftmailer.transport.eventdispatcher']
    );

    return $transport;
});

include __DIR__.'/src/Idev/Controller/controllers_backend.php';
include __DIR__.'/src/Idev/Controller/controllers_frontend.php';

$app->run();
<?php

use Symfony\Component\HttpFoundation\Request;

$app->get('/admin', function () use ($app) {
	return $app['twig']->render('backend/index.html', array(

	));
})
->bind('admin_dashboard');

$app->get('/admin/login', function(Request $request) use ($app) {
    return $app['twig']->render('backend/login.html', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})
->bind('admin_login');
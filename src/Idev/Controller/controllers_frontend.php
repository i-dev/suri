<?php

$app->get('/', function () use ($app) {
    
    $em = $app['orm.em'];

    
    return $app['twig']->render('frontend/index.html', array(
            
    ));
    
    
})
->bind('homepage');
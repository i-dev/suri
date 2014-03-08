<?php

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));


$app->register(new DerAlex\Silex\YamlConfigServiceProvider($app['pathroot'].'/app/config.yml'));
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Neutron\Silex\Provider\ImagineServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider(), array(
        'session.storage.save_path' => $app['pathroot'].'/web/cache/sessions'
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

$captcha = new Kilte\Silex\Captcha\CaptchaServiceProvider();

$app->register($captcha);
$app->mount('/', $captcha);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => $app['pathroot'].'/src/Idev/View',
));


$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return sprintf($app['request']->getBasePath().'/web/assets/%s', ltrim($asset, '/'));
    }));

        $twig->addFunction(new \Twig_SimpleFunction('image_resize', function ($filename, $width, $length) use ($app) {

            $fName = str_replace('/', '-', $filename);
            $extension = pathinfo($app['pathroot'].'/web/assets/'.$filename, PATHINFO_EXTENSION);

            $resizedFilename = '/web/cache/images/'.$fName.'-'.$width.'x'.$length.'.'.$extension;
            $realFile = sprintf($app['pathroot'].'/web/assets/%s', ltrim($filename, '/'));

            if ( !file_exists($resizedFilename) || filemtime($resizedFilename) < filemtime($realFile) ) {
                $app['imagine']
                ->open($realFile)
                ->resize(new Imagine\Image\Box($width, $length))
                ->save($app['pathroot'].$resizedFilename);
            }
            return sprintf($app['request']->getBasePath().'/%s', ltrim($resizedFilename, '/'));
        }));

        $twig->addGlobal('config', $app['config']);
        return $twig;
}));


$app->register(new Silex\Provider\DoctrineServiceProvider, array(
        "db.options" => $app['config']['database']
));
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider, array(
        "orm.proxies_dir" => $app['pathroot']."/web/cache/doctrine",
        "orm.em.options" => array(
                "mappings" => array(
                        array(
                                "type" => "annotation",
                                "namespace" => "Idev\Entity",
                                "path" => $app['pathroot'].'/src/Idev/Entity',
                                "use_simple_annotation_reader" => false
                        ),
                ),
        ),
));


$app->register(new Silex\Provider\SecurityServiceProvider(), array(
        'security.firewalls' => array(
                'admin' => array(
                        'pattern' => '^/admin',
                        'anonymous' => true,
                        'form' => array('login_path' => '/admin/login', 'check_path' => '/admin/login_check'),
                        'logout' => array('logout_path' => '/admin/logout'),
                        'users' => $app->share(function($app) { return new Idev\Security\UserProvider($app['orm.em']); }),
                ),

        ),
));


$app['db.event_manager']->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
$app['db.event_manager']->addEventSubscriber(new \Gedmo\Sluggable\SluggableListener());




$app['security.role_hierarchy'] = array(
        'ROLE_ADMIN' => array('ROLE_USER'),
        'ROLE_SUPERADMIN' => array('ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'),
);

$app['security.encoder.digest'] = $app->share(function () use ($app) {
    return new Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('sha1', false, 1);
});

$app['security.access_rules'] = array(
        array('^/admin/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/admin', 'ROLE_ADMIN'),
);

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
    

$app['user'] = $app->share(function($app) {
    $token = $app['security']->getToken();

    return (null !== $token) ? $token->getUser() : null;
});

$app['flashbag'] = $app->share(function (Silex\Application $app) {
    return $app['session']->getFlashBag();
});

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
        'locale_fallbacks' => array('fr'),
        'locale' => 'fr'
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new  Symfony\Component\Translation\Loader\YamlFileLoader());

    $translator->addResource('yaml', $app['pathroot'].'/src/Idev/Locales/locale.fr.yml', 'fr');

    return $translator;
}));

$app->register(new Silex\Provider\ValidatorServiceProvider());
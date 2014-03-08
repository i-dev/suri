<?php

/** Class loader **/
$loader = require_once __DIR__.'/vendor/autoload.php';
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

/*************** Init application ***************/
$app = new Silex\Application();

/********** Ajout des services **********/

$app->register(new LExpress\Silex\ConsoleServiceProvider(), array(
        'console.name'    => 'Wahou',
        'console.version' => '1.0',
));

$configFile = __DIR__.'/app/config-locale.yml';

$app->register(new DerAlex\Silex\YamlConfigServiceProvider($configFile));


/***** Doctrine configuration *****/
$app->register(new Silex\Provider\DoctrineServiceProvider, array(
        "db.options" => $app['config']['database']
));
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider, array(
        "orm.proxies_dir" => __DIR__."/web/cache/doctrine",
        "orm.em.options" => array(
                "mappings" => array(
                        array(
                                "type" => "annotation",
                                "namespace" => "Idev\Entity",
                                "path" => __DIR__.'/src/Idev/Entity',
                                "use_simple_annotation_reader" => false
                        ),
                ),
        ),
));

$em = $app['orm.em'];

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));

$app['console']->setHelperSet($helperSet);
Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($app['console']);



/*************** Fin de déclaration des controlleurs ***************/
/*******************************************************************/
/*************** Lancement de l'application ***************/

$app['console']->run();
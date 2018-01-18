<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Form\FormRenderer;
use Keywords\Controllers\NodesController;
use Symfony\Component\Asset\Package;

$app = new Application();

$app->register(new DoctrineServiceProvider(), array (
    'db.options' => array(
        'driver' => 'pdo_sqlite',
        // 'path' => __DIR__ . '/../database/cloud-migration.db'
        'path' => '/var/tmp/db'
    ),
));

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.named_packages' => array(
        'css' => array('version' => 'css3', 'base_path' => '/css'),
        'images' => array('base_path' => '/img'),
    ),
));

$app->register(new ServiceControllerServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new TranslationServiceProvider(), array('translator.domains' => array()));

$app['debug'] = true;

$app['keywords.controller'] = function() use ($app) {
    return new NodesController();
};

$app->register(
    new TwigServiceProvider(),
    ['twig.path' => __DIR__ . '/../views']
);

$app->extend('twig.runtimes', function ($runtimes, $app) {
    return array_merge($runtimes, [
        FormRenderer::class => 'twig.form.renderer',
    ]);
});

$app->get(
    '/',
    'keywords.controller:getAll'
);

$app->post(
    '/keywords',
    'Keywords\\Controllers\\NodesController::create'
);

$app->get(
    '/keywords',
    'Keywords\\Controllers\\NodesController::getNewForm'
);

$app->run();


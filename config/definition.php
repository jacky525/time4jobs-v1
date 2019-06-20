<?php

use Noodlehaus\Config;

$envSettings = \Noodlehaus\Config::load(BASE_PATH . '/env.php');
$isDebug = $envSettings->get('APP_DEBUG', 'true');

//Configuring PHP-DI

$default = [
    'config' => DI\create(\Noodlehaus\Config::class)->constructor(APP_ROOT . '/config'),
    'settings.displayErrorDetails' => $isDebug,
    'settings.routerCacheFile' => function (\Psr\Container\ContainerInterface $c) {
        $config = $c->get('config');
        return $config->get('cache.path.router');
    },
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $c) {
        $twig = new \Slim\Views\Twig(APP_ROOT .'/app/templates', [
            'cache' => APP_ROOT .'/cache'
        ]);

        $twig->addExtension(new \Slim\Views\TwigExtension(
            $c->get('router'),
            $c->get('request')->getUri()
        ));

        return $twig;
    },
    'TestController' => function (\Psr\Container\ContainerInterface $container) {
        return new Slim\Controllers\TestController();
    },
    'MainController' => function (\Psr\Container\ContainerInterface $container) {
        return new Slim\Controllers\MainController($container);
    },
    'FileCache' => function (\Psr\Container\ContainerInterface $container) {
        $slim = new \DI\Bridge\Slim\App;
        $cache =  new \SNicholson\SlimFileCache\Cache( $slim,APP_ROOT .'/cache');
        return $cache;
    },
    'logger' => function (\Psr\Container\ContainerInterface $container) {
        $filePath = APP_ROOT .'/log/'; //指定目錄
        // JBMLog in file
        $jbHandler = new \Corp104\Common\Logger\JBLog\JBMLogHandler();
        $jbHandler->setCustomLogPath($filePath);

        // mail  lab: oms02.e104.com.tw
//        $jbHandler = new \Corp104\Common\Logger\JBLog\JBMLogMailHandler([
//            'host' => 'oms02.e104.com.tw',
//            'port' => 25,
//            'mailTo' => [
//                'jbcpg@104.com.tw',
//            ],
//        ]);
//        $jbHandler->addMailTo("jacky.lin@104.com.tw");

        $logger = new \Monolog\Logger('logger');
        $logger->pushHandler($jbHandler);

        return $logger;
    },
];

return $default;

<?php
use \Psr\Container\ContainerInterface;
use Noodlehaus\Config;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Contracts\Cache\ItemInterface;

//Configuring PHP-DI

$default = [
    'config' => DI\create(Config::class)->constructor(APP_ROOT . '/config'),
    'settings.displayErrorDetails' => getenv('APP_DEBUG'),
    'settings.routerCacheFile' => function (ContainerInterface $c) {
        $config = $c->get('config');
        return $config->get('cache.path.router');
    },
    'settings.responseChunkSize' => 4096,
    'settings.outputBuffering' => 'append',
    'settings.determineRouteBeforeAppMiddleware' => false,
    \Slim\Views\Twig::class => function (ContainerInterface $c) {
        $twig = new \Slim\Views\Twig(
            APP_ROOT .'/app/templates',
            [
            'cache' => APP_ROOT .'/cache'
            ]
        );

        $twig->addExtension(
            new \Slim\Views\TwigExtension(
                $c->get('router'),
                $c->get('request')->getUri()
            )
        );

        return $twig;
    },
    'TestController' => function (ContainerInterface $container) {
        return new Slim\Controllers\TestController();
    },
    'MainController' => function (ContainerInterface $container) {
        return new Slim\Controllers\MainController($container);
    },
    'logger' => function (ContainerInterface $container) {
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
    // cache
    CacheInterface::class => function (ContainerInterface $container) {
        return $container->get(ApcuCache::class);
    },
    ApcuCache::class => function (ContainerInterface $container) {
        return new ApcuCache('', 0);
    },
    FilesystemCache::class => function (ContainerInterface $container) {
        $config = $container->get('config');
        return new FilesystemCache('', 0, $config->get('cache.path.filesystemCache'));
    },
];

return $default;

<?php
// route
use \Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Simple\ApcuCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Contracts\Cache\ItemInterface;
use \Slim\Views\Twig;

// method 1 class and callback
$app->get('/hello/{name}', ['MainController', 'hello']);
// method 2 call class
$app->get('/hello', 'TestController');

/**
 * @param $response
 * @param \Slim\Views\Twig $twig
 * @param \Psr\Container\ContainerInterface $c
 * @return \Psr\Http\Message\ResponseInterface
 */
// method 3 Service and Request or response injection
$app->get('/', function ($response, Twig $twig, ContainerInterface $container) {
    $cache = $container->get(ApcuCache::class);

    if (!$cache->has('my_cache_key')) {
        $cache->set('my_cache_key','foobar',10);
    } else {
        $value = $cache->get('my_cache_key');
    }
    echo $value;
    echo "<br>=======<br>";

    $cache1 = $container->get(FilesystemCache::class);

    if (!$cache1->has('my_cache_key')) {
        $cache1->set('my_cache_key','foobar',10);
    } else {
        $value1 = $cache1->get('my_cache_key');
    }
    echo $value1;


//    $logger=$c->get('logger');

    // logger
//    try {
//        throw new \Exception("new Exception~");
//    } catch (\Exception $e) {
//        $logger->error("here has error.", array($e));
//    }

    return $twig->render($response, 'home.twig');
});

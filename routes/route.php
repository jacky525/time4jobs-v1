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
    $apcucache = $container->get(ApcuCache::class);

    if (!$apcucache->has('my_cache_key')) {
        $apcucache->set('my_cache_key','foobar',10);
    } else {
        $my_apcucache_value = $apcucache->get('my_cache_key');
    }
    echo $my_apcucache_value;
    echo "<br>=======<br>";

    $filecache = $container->get(FilesystemCache::class);

    if (!$filecache->has('my_cache_key')) {
        $filecache->set('my_cache_key','foobar',10);
    } else {
        $my_filecache_value = $filecache->get('my_cache_key');
    }
    echo $my_filecache_value;


//    $logger=$c->get('logger');

    // logger
//    try {
//        throw new \Exception("new Exception~");
//    } catch (\Exception $e) {
//        $logger->error("here has error.", array($e));
//    }

    return $twig->render($response, 'home.twig');
});

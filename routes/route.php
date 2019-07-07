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

// method 3 for test case
$app->get('/api/user/{id}', function ($id, $request, $response) {

//    echo json_encode($data);

    return $response->withJSON(
        ['id' => $id],
        200,
        JSON_UNESCAPED_UNICODE
    );
});
/**
 * @param $response
 * @param \Slim\Views\Twig $twig
 * @param \Psr\Container\ContainerInterface $c
 * @return \Psr\Http\Message\ResponseInterface
 */
// method 4 Service and Request or response injection
// param_name For “Unlimited” optional parameters
$app->get(
    '/{param_name:.*}',
    function ($param_name, $response, Twig $twig, ContainerInterface $container) {
        $apcucache = $container->get(ApcuCache::class);

        $my_apcucache_value = "";
        if (!$apcucache->has('my_cache_key')) {
            $apcucache->set('my_cache_key', 'foobar', 10);
        } else {
            $my_apcucache_value = $apcucache->get('my_cache_key');
        }
        echo $my_apcucache_value;
        echo "<br>====ssssss===<br>";

        $filecache = $container->get(FilesystemCache::class);

        $my_filecache_value = "";
        if (!$filecache->has('my_cache_key')) {
            $filecache->set('my_cache_key', 'foobar', 10);
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

        return $twig->render($response, 'home.twig', [
            'name' => $param_name
        ]);
    }
);

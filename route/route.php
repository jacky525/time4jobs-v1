<?php
// route

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
$app->get('/', function ($response, \Slim\Views\Twig $twig, \Psr\Container\ContainerInterface $c) {
    $logger=$c->get('logger');

    // logger
    try {
        throw new \Exception("new Exception~");
    } catch (\Exception $e) {
        $logger->error("here has error.", array($e));
    }

    return $twig->render($response, 'home.twig');
});

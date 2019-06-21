<?php
namespace Slim\Controllers;
use \Slim\Services\TestService;
use Symfony\Component\Cache\Simple\ApcuCache;

/**
 * Class MainController
 *
 * @package Slim\Controllers
 */
class MainController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var TestService
     */
    private $testService;
    /**
     * MainController constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
        $this->testService = new TestService();
    }

    /**
     * @param $name
     * @param $request
     * @param $response
     * @return mixed
     */
    public function hello($name , $request, $response )
    {
        $cache=$this->container->get(ApcuCache::class);
        $ttl = $this->container->get('config')->get('cache.TestService.ttl');

        // cache key must be string
        $psid='str11';
        if (!$cache->has($psid)) {
            $result = $this->testService->getResult();
            $cache->set($psid, $result, $ttl);

        } else {
            $result = $cache->get($psid);
        }

        $response->getBody()->write("Hello, $name  <br> $result");
        return $response;
    }

}

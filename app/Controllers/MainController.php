<?php
namespace Slim\Controllers;
use \Slim\Services\TestService;
/**
 * Class MainController
 * @package Slim\Controllers
 */
class MainController
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    private $testService;
    /**
     * MainController constructor.
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
        // cache key must be string
        $psid='test11';

        $result = $this->testService->iscached($psid,$this->container);
        if(empty($result))
        {
            $result = $this->testService->getResult();
            $this->testService->cache($psid,$result,$this->container);
        }
        $response->getBody()->write("Hello, $name  $result");
        return $response;
    }

}

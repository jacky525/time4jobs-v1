<?php
namespace Time4job\Controllers;

use Psr\SimpleCache\CacheInterface;
use \Time4job\Services\TestService;

//use Illuminate\Support\Collection;


/**
 * @OA\Info(title="My First API", version="0.1")
 */
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
     * @OA\Get(
     *     path="/hello/{name}",
     *     summary="取得 name",
     *     description="這不是個api介面,這個返回一個頁面",
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string"), required=true, description="使用者ID"),
     *     @OA\Response(
     *      response="200",
     *      description="An example resource"
     *     )
     * )
     */
    /**
     * @param $name
     * @param $request
     * @param $response
     * @return mixed
     */
    public function hello($name, $request, $response)
    {
        $cache=$this->container->get(CacheInterface::class);
        $ttl = $this->container->get('config')->get('cache.TestService.ttl');

        // cache key must be string
        $psid='str11';
        if (!$cache->has($psid)) {
            $result = $this->testService->getResult();
            $cache->set($psid, $result, $ttl);
        } else {
            $result = $cache->get($psid);
        }

        $authors = new \Slim\Collection([1,2,3]);
        echo $authors->get(2);

        //$response->getBody()->write("Hello, $name  <br> $result");
        //return $response;

        //這裡透過 user service 從cookie 取得 idno
        $idno = "313774697013264584";
        return $this->container->get('twig')->render(
            $response,'home.twig',
            [
                'idno' => (int)$idno
            ]
        );
    }
}

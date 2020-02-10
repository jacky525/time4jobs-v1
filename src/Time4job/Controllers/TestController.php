<?php
namespace \Time4job\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

/**
 * Class TestController
 *
 * @package Slim\Controllers
 */
class TestController
{
    /**
     * TestController constructor.
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function __invoke()
    {
        echo "hello world";
    }

    public function hello():string
    {
        return "hello world";
    }
}

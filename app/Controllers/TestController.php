<?php
namespace Slim\Controllers;

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
        echo 'i\'m in test controll<br>';
    }

    /**
     *
     */
    public function __invoke()
    {
        echo "hello world";
    }
}
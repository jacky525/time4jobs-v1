<?php
namespace Slim\Services;

/**
 * Class TestService
 * @package Slim\Services
 */
class TestService extends ServiceCache
{

    /**
     * @return mixed|string
     */
    public function getResult()
    {
        return "no cache";
    }
}
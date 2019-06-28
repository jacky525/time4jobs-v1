<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Psr\Container\ContainerInterface;
use Slim\App;

class Functional extends \Codeception\Module
{
    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {

        /** @var App $app */
        $app = require dirname(__DIR__) . '/../../bootstrap/app.php';
        return $app->getContainer();
    }
}

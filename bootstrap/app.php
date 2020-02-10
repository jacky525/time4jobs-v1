<?php

use \DI\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;

if (!defined('APP_ROOT')) define('APP_ROOT', dirname(dirname(__FILE__)));

require APP_ROOT . '/vendor/autoload.php';

// Load "Environments" files.
$dotenv = new Dotenv();
$dotenv->load(APP_ROOT.'/.env');


//When you deploy new versions of your code to production you must delete the generated file
//(or the directory that contains it) to ensure that the container is re-compiled.
$app = new class() extends \DI\Bridge\Slim\App {

    protected function configureContainer(ContainerBuilder $builder)
    {
        // Definition file for testing environment
        $env = getenv('APP_ENV');

        if ($env !== 'testing') {
            //  The cache relies on APCu directly because it is the only cache system
            $builder->enableDefinitionCache();
            $builder->enableCompilation(APP_ROOT . '/cache/compiler-cache/compiler');
        }

        // Main definition
        $builder->addDefinitions(APP_ROOT . '/src/Time4job/definition.php');
    }
};

return $app;

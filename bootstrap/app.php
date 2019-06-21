<?php

use DI\ContainerBuilder;
use \Noodlehaus\Config;

define('APP_ROOT', dirname(dirname(__FILE__)));
require APP_ROOT . '/vendor/autoload.php';

// Load "Environments" files.
$envSettings = Config::load(APP_ROOT . '/env.php');


//When you deploy new versions of your code to production you must delete the generated file
//(or the directory that contains it) to ensure that the container is re-compiled.

$app = new class() extends \DI\Bridge\Slim\App {

    protected function configureContainer(ContainerBuilder $builder)
    {

        //  The cache relies on APCu directly because it is the only cache system
        $builder->enableDefinitionCache();

        $builder->enableCompilation(APP_ROOT . '/cache/compiler-cache/compiler');

        // Main definition
        $builder->addDefinitions(APP_ROOT . '/config/definition.php');

    }
};

/*
 * Configure database ORM
 */
$container = $app->getContainer();
$config = $container->get('config');
$connections = $config->get('database.connections');

$capsule = new \Illuminate\Database\Capsule\Manager;
foreach ($connections as $connectionName => $setting) {
    $capsule->addConnection($setting, $connectionName);
}
$capsule->setAsGlobal();
$capsule->bootEloquent();

require APP_ROOT . '/routes/route.php';
// Run!
$app->run();

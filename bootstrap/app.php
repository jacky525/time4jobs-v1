<?php

use DI\ContainerBuilder;

define('APP_ROOT', dirname(dirname(__FILE__)));
require APP_ROOT . '/vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));


// Load "Environments" files.
$envSettings = \Noodlehaus\Config::load(BASE_PATH . '/env.php');

// Timezone.
date_default_timezone_set($envSettings->get('TIMEZONE', 'UTC'));
// Encoding.
mb_internal_encoding('UTF-8');



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

require BASE_PATH . '/route/route.php';
// Run!
$app->run();

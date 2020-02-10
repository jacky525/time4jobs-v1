<?php
$app = require __DIR__ . '/../bootstrap/app.php';

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

//route for Time4job
require APP_ROOT . '/src/Time4job/route.php';

// Run!
$app->run();

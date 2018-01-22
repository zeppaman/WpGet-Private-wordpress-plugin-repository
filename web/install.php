<?php

namespace WpGet;

use \WpGet\utils\DependencyManager as DependencyManager;


require '../vendor/autoload.php';
require '../src/utils/DependencyManager.php';

$appPath=realpath("../../");

$dm= new DependencyManager($appPath);

$dm->requireOnceFolders( array(
  'src/models',
  'src/controllers/base',
  'src/controllers',
  'src/db/base',
  'src/db',
  'src/handlers',
  'src/utils',
));

// Slim configuration

// $app = new \Slim\App(['settings'=> $config]);


// $container = $app->getContainer();


// $container['logger'] = function($c)
// {
//   $logger = new Logger('api');
  
//    $rotating = new RotatingFileHandler(__DIR__ . "../logs/api.log", 0, Logger::DEBUG);
//    $logger->pushHandler($rotating);
//    return $logger;
// };

 
// $container['db'] = function ($container) {
//   $capsule = new \Illuminate\Database\Capsule\Manager;
//   $capsule->addConnection($container['settings']['db']);
//   $capsule->setAsGlobal();
//   $capsule->bootEloquent();

//   $capsule->getContainer()->singleton(
//     Illuminate\Contracts\Debug\ExceptionHandler::class,
//     ErrorHandler::class
//   );

//   return $capsule;
// };


// $capsule = $container['db'];





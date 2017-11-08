<?php

namespace WpGet;


ini_set('display_errors', 'On');
ini_set('html_errors', 0);
error_reporting(-1);





use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Controllers\UserController as UserController;
use \WpGet\Controllers\DynamicController as DynamicController;
use \WpGet\Controllers\EntityController as EntityController;
use \WpGet\Controllers\RepositoryController as RepositoryController;
use \WpGet\db\UpdateManager as UpdateManager;
use \WpGet\db\TableBase as TableBase;
use \WpGet\db\RepositoryTable as RepositoryTable;
use \WpGet\db\UsersTable as UsersTable;
use \WpGet\db\PublishTokenTable as PublishTokenTable;
use \WpGet\handler\ErrorHandler as ErrorHandler;
use \WpGet\utils\DependencyManager as DependencyManager;
use \WpGet\Controllers\AuthenticationController as AuthenticationController;

use \Monolog\Logger as Logger;
use \Monolog\Handler\RotatingFileHandler as RotatingFileHandler;
use \Bairwell\MiddlewareCors as MiddlewareCors;


require '../../vendor/autoload.php';
require '../../src/utils/DependencyManager.php';

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


$configPath=$dm->resolvePath('config/settings.php');
$config =  include($configPath);



// Slim configuration

$app = new \Slim\App(['settings'=> $config]);

$container = $app->getContainer();

 
$container['logger'] = function($c)
{
  $logger = new Logger('auth');
  
   $rotating = new RotatingFileHandler(__DIR__ . "../../../logs/auth.log", 0, Logger::DEBUG);
   $logger->pushHandler($rotating);
   return $logger;
};


 
$container['db'] = function ($container) {
  $capsule = new \Illuminate\Database\Capsule\Manager;
  $capsule->addConnection($container['settings']['db']);
  $capsule->setAsGlobal();
  $capsule->bootEloquent();

  $capsule->getContainer()->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    ErrorHandler::class
  );

  return $capsule;
};


$capsule = $container['db'];


// $container['errorHandler'] = function ($c) {
//   return function ($request, $response, $exception) use ($c) 
//   {
//     $logger=$c["logger"];
//     $logger->
//       return $c['response']->withStatus(500)
//                            ->withHeader('Content-Type', 'text/html')
//                            ->write('Something went wrong!');
//   };
// };

$app->add(new MiddlewareCors( $container['settings']['cors'])); 






$dm->upgradeDB($container['settings']['db']);


  
$app->any('/{action}', AuthenticationController::class);
// Run app
$app->run();


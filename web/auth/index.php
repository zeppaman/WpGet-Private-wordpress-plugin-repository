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
use \Tuupola\Middleware\Cors as Cors;



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

$logger = new Logger("slim");
$rotating = new RotatingFileHandler(__DIR__ . "slim.log", 0, Logger::DEBUG);
$logger->pushHandler($rotating);

$configPath=$dm->resolvePath('config/settings.php');
$config =  include($configPath);



// Slim configuration

$app = new \Slim\App(['settings'=> $config]);

$container = $app->getContainer();

$config["cors"]["logger"]=  $logger;

// $app->add(function($request, $response, $next) {
//   $route = $request->getAttribute("route");

//   $methods = [];

//   if (!empty($route)) {
//       $pattern = $route->getPattern();

//       foreach ($this->router->getRoutes() as $route) {
//           if ($pattern === $route->getPattern()) {
//               $methods = array_merge_recursive($methods, $route->getMethods());
//           }
//       }
//       //Methods holds all of the HTTP Verbs that a particular route handles.
//   } else {
//       $methods[] = $request->getMethod();
//   }
  
//   $response = $next($request, $response);

  
//   return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods))->withHeader("Access-Control-Allow-Origin", "*");
// });

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../../logs/app.log");
    $logger->pushHandler($file_handler);
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


$container['errorHandler'] = function ($c) {
  return function ($request, $response, $exception) use ($c) {
      return $c['response']->withStatus(500)
                           ->withHeader('Content-Type', 'text/html')
                           ->write('Something went wrong!');
  };
};


$dm->upgradeDB($container['settings']['db']);


  
  $app->any('/{action}', AuthenticationController::class);
  $app->add(new Cors( $config['cors'])); 
// Run app
$app->run();


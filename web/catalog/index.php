<?php

namespace WpGet;


// ini_set('display_errors', 'On');
// ini_set('html_errors', 0);
// error_reporting(-1);





use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Controllers\CatalogController as CatalogController;
use \WpGet\Controllers\DynamicController as DynamicController;
use \WpGet\handler\ErrorHandler as ErrorHandler;
use \WpGet\utils\DependencyManager as DependencyManager;



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
));

$configPath=$dm->resolvePath('config/settings.php');
$config =  include($configPath);



// Slim configuration

$app = new \Slim\App(['settings'=> $config]);


$container = $app->getContainer();

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
  
 

  //Routing configuration

  $app->any('/{action}[/{id}]', CatalogController::class);

// Run app
$app->run();
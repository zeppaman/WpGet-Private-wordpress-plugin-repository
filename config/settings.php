<?php  
return [  
  'determineRouteBeforeAppMiddleware' => false,
  'outputBuffering' => false,
  'displayErrorDetails' => true,
  'db' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'wpget',
    'username' => 'wpget',
    'password' => 'WpGet2017!',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'name' =>'default'
  ],
  'cors'=>[
    "origin" => ["http://localhost:4200"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["content-type","origin"],
    "headers.expose" => []
]
];
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
    'origin'           => ['*'],
    'exposeHeaders'    => '',
    'maxAge'           => 120,
    'allowCredentials' => true,
    'allowHeaders'     => ['Accept', 'Accept-Language', 'Authorization', 'Content-Type','DNT','Keep-Alive','User-Agent','X-Requested-With','If-Modified-Since','Cache-Control','Origin'],
  ],
  'packageManager'=>
  [
    'tempdir'=>"temp",
    'storagedir'=>"storage"
  ]
];
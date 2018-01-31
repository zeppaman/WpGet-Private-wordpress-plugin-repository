<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \Monolog\Logger as Logger;

 class DynamicController
{
    protected $logger;
    protected $container;
    protected $request;
    
       public function __construct( $container)
       {
          
           $this->container=$container;
           $this->logger = $container["logger"];
       }

    public function __invoke($request, $response, $args)  
    {   
        try
        {
            $this->request=$request;
            $params = explode('/', $request->getAttribute('params'));
            $method = $request->getMethod();
            $action= $request->getAttribute('action');
            $functioname=strtolower($method).$action;
    

           $this->logger->info("Dynamic Route fired: $method $action  $functioname");
            
            // if( $method=="OPTIONS" &&  !isset($this->$functioname))
            // {
            //     return $response;
            // }
            return  $this->$functioname($request, $response, $args);
        }
        catch(\Exception $e)
        {
            $this->logger->error($e);
            return $response;
        }
    }
}
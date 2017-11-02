<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;

 class DynamicController
{
    public function __invoke($request, $response, $args)  
    {   
       
        $params = explode('/', $request->getAttribute('params'));
        $method = $request->getMethod();
        $action= $request->getAttribute('action');
        $functioname=strtolower($method).strtolower($action);
  
     
        return $this->$functioname($request, $response, $args);
    }
}
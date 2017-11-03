<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 class CatalogController extends DynamicController
{
    function getStatus($request, $response, $args)
    {   
        $charset = $app->request->headers->get('ACCEPT_CHARSET');   
        $response->getBody()->write( "OK");
    }

   
}
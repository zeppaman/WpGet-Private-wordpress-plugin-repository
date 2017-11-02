<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;

 class UserController extends DynamicController
{
   // use DynamicController;
    // public function __invoke($request, $response, $args)  
    // {
        
  
       
    //     return parent:invoke($request, $response, $args);
    // }
 

        
        //#http://discourse.slimframework.com/t/slim-3-routing-best-practices-and-organization/93/8
    function getAll($request, $response, $args) {     
        
        return $response->getBody()->write(User::all()->toJson());
    }

    function getItem ($request, $response, $args) {
        $id = $args['id'];
        $dev = User::find($id);
        $response->getBody()->write($dev->toJson());
        return $response;
    }


    function postItem($request, $response, $args) 
    {
        $data = $request->getParsedBody();
        $dev = new User();
        $dev->username = $data['username'];
        $dev->password = $data['password'];
        $dev->token = $data['token'];

        $dev->save();

        return $response->withStatus(201)->getBody()->write($dev->toJson());
    }

    function deleteItem($request, $response, $args) 
    {
       $id = $args['id'];
       $dev = User::find($id);
       $dev->delete();
       return $response->withStatus(200);
    }

    
     function putItem($request, $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $dev = User::find($id);
        $dev->username = $data['username'];
        $dev->password = $data['password'];
        $dev->token = $data['token'];

        $dev->save();

        return $response->getBody()->write($dev->toJson());
    }
}
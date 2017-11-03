<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \WpGet\Controllers\EntityController as EntityController;
use \WpGet\Utils\Util as Util;

 class AuthenticationController extends ProtectedController
{

    public function postAuthorize(  $request, $response, $args)
    {
        try
        {
        $parsedBody = $request->getParsedBody();
        $username=$parsedBody["username"];
        
        $users=User::where('username', '=', $username)->get();
        $user=User::find($users[0]->id);

        
        $token= Util::generateToken();

       $user->token=$token;
       
        $user->save();

        $response->getBody()->write($user->toJson());

        }
        catch(\Exception $e)
        {
            print_r($e);
        }

    }


    public function getAuthorize(  $request, $response, $args)
    {
        try
        {
            $user=$this->getUser($request);
            
            if(!isset($user) ) 
            {
                return $response=  $response->withStatus(403);    
            }
            
       
           return $response=$response->withJson($user);
        }
        catch(\Exception $e)
        {
            print_r($e);
        }

    }
  
}
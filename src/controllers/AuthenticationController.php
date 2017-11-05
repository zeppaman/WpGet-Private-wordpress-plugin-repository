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
        $this->logger->info("Authorize started");
        try
        {
        $parsedBody = $request->getParsedBody();
        $this->logger->info("request".json_encode($parsedBody));
        $username=$parsedBody["username"];
        
        $users=User::where('username', '=', $username)->get();
        if(!isset($users) || sizeof( $users)==0)
        {
            $this->logger->info("user not found");
            $response=$response->withStatus(403); 
            return $response;
        }
        $user=User::find($users[0]->id);
        
        $this->logger->info("user found");
        $hp=hash('sha512',$parsedBody["password"]);
       
        $this->logger->info("computing password".$hp);
        if($hp!=$user->password)
        {
            $this->logger->info("password not matching");
            $response= $response->withStatus(403); 
            return $response;
        }
       

        //Update access token
        $token= Util::generateToken();
        $user->token=$token;
        $user->save();
        $this->logger->info("Access token updated");

        
        
        $response->getBody()->write($user->toJson());



        return $response;
        }
        catch(\Exception $e)
        {
            $this->logger->error((string)$exception);
            return  $response->withStatus(403);    
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
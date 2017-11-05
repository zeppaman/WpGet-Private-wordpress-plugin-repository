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
        
        $user=User::find($users[0]->id);
        
        $this->logger->info("user found");
        $hp=hash('sha512',$parsedBody["password"]);
       
        $this->logger->info("computing password".$hp);
        if($hp!=$user->password)
        {
            $this->logger->info("password not matching");
            throw new \Exception("Password mismatch, failed login for   $username");
        }
       

        //Update access token
        $token= Util::generateToken();
        $user->token=$token;
        $user->save();
        $this->logger->info("Access token updated");

        $headers = $response->getHeaders();
        foreach ($headers as $name => $values) {
            $this->logger->info( "given : ".$name . ": " . implode(", ", $values));
        }
        
        $response->getBody()->write($user->toJson());


        foreach ($headers as $name => $values) {
            $this->logger->info( "given (2) : ".$name . ": " . implode(", ", $values));
        }

        return $response;
        }
        catch(\Exception $e)
        {
            $this->logger->error((string)$exception);
            return $response=  $response->withStatus(403);    
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
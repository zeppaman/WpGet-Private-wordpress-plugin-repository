<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \WpGet\Models\UserInfo as UserInfo;
use \WpGet\Models\PublishToken as PublishToken;


class ProtectedController extends DynamicController
{
    public function getUser($request)
    {
        $u=$this->getServiceUser($request);
        if(!isset($u))
        {
            
            $u= $this->getUiUser($request);
        }

        return $u;
    }

    public function getUiUser($request)
    {
        $tokenHeader= $request->getHeaderLine('Authorization');
        
         if(!isset( $tokenHeader) || strlen($tokenHeader)<5)
         {
             return null;
         }
         
         $token=substr($tokenHeader,strlen("Bearer "));

         $users=User::where('token', '=', $token)->get();
         if( sizeof( $users) !=1)
         {
            return null;
         }
         $user=$users[0];

         $ui= new UserInfo();
         $ui->roles=array("admin");
         $ui->user= json_decode( $user->toJson());
         $ui->token=$token;
         return $ui;
    }


    public function getServiceUser($request)
    {
        $tokenHeader= $request->getHeaderLine('Authorization');
        
         if(!isset( $tokenHeader) || strlen($tokenHeader)<5)
         {
             return null;
         }
         
         $token=substr($tokenHeader,strlen("Bearer "));

         $roles=array();

         $this->checkToken("write",$token,$roles);
         $this->checkToken("read",$token,$roles);

         if(sizeof($roles)==0)
         {
             return null;
         }

         $ui= new UserInfo();
         $ui->token=$token;
         $ui->roles=$roles;
         return $ui;
    }

    public function checkToken($mode,$token,&$roles)
    {
        $pt=PublishToken::where($mode."token", '=', $token)->get();         
        if( sizeof( $pt) >0)
        {
            foreach($pt as $key)
            {
            
            $roles[]=$mode."_".$key->reposlug;
            }
        }

    }

}
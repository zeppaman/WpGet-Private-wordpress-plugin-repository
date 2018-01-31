<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \WpGet\Models\UserInfo as UserInfo;
use \WpGet\Models\PublishToken as PublishToken;


class ProtectedController extends DynamicController
{
    public function getUser()
    {
        $u=$this->getServiceUser($this->request);
        if(!isset($u))
        {
            
            $u= $this->getUiUser();
        }
   
       
        return $u;
    }

    public function getUiUser()
    {
        $tokenHeader= $this->request->getHeaderLine('Authorization');
        $token=null;
        $data = $this->request->getParsedBody();

       

        //to support header and data
        if(!isset( $tokenHeader) || strlen($tokenHeader)<5)
        {
            $token= $data["token"];
        }
        else
        {
           
            //separate token
            if(strpos($tokenHeader,"Bearer")==0)
            {
               
                $token=substr($tokenHeader,strlen("Bearer "), strlen($tokenHeader));
            }
        }
       
       
        if(!isset( $token) || strlen($token)<5)
        {
            return null;
        }
        

         
         

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
         $ui->type="UI";

         return $ui;
    }


    public function getServiceUser()
    {
        $tokenHeader= $this->request->getHeaderLine('Authorization');
        
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
         $ui->type="SERVICE";
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


    public function authorize($roles=array())
    {
        $user=$this->getUser();
        if(!isset($user))
        {
            header('HTTP/1.0 401 Unautorized'); 
            die('You are not allowed to access this file.'); 
        }
        if($roles && sizeof($roles)>0)
        {
            foreach($roles as $roletoCheck)
            {
                if(in_array($roletoCheck,$user->roles))
                {
                    return;
                } 
            }
            header('HTTP/1.0 403 Forbidden'); 
            die('You are not allowed to access this file.'); 
       }
     
    }

  
      
    

}
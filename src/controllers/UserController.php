<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \WpGet\Controllers\EntityController as EntityController;
use \WpGet\db\UsersTable as UsersTable;

 class UserController extends EntityController
{
    public function getTableDefinition()
    {   
         
        return new UsersTable();
    }

     public function getModel()
     {
        // echo "getmodel";
         return '\WpGet\Models\User';
     }

     function postItem($request, $response, $args) 
     {
         //check for duplicated users
       
       return parent::postItem($request, $response, $args);

     }


     function pushItem($request, $response, $args) 
     {
         //check for duplicated users

       return parent::pushItem($request, $response, $args);

     }

     function processInput(&$entity,&$data)
     {
       
        if(isset($data["password"]) && strlen($data["password"])>0)
        {
            $data["password"]=hash('sha512',$data["password"]);
        }
        else
        {
            unset($data["password"]);
        }
     }

    //  function presave(&$entity,$data)
    //  {
    //      if(isset($data["password"]) && strlen($data["password"])>0)
    //      {
    //         $entity->password=hash('sha512',$data["password"]);
    //      }
    //  }

     function process(&$item)
     {
        $item->password=null;
     }
}
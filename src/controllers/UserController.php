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
}
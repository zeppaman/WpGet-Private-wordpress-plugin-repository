<?php
namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Illuminate\Support\Facades;
use \WpGet\db\PublishTokenTable as PublishToken;

 class PublishTokenController extends EntityController
{
    
    
     public function getTableDefinition()
     {   
          
         return new PublishToken();
     }

      public function getModel()
      {
         // echo "getmodel";
          return '\WpGet\Models\PublishToken';
      }
}
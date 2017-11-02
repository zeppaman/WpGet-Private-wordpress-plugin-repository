<?php
namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Illuminate\Support\Facades;
use \WpGet\db\RepositoryTable as RepositoryTable;

 class RepositoryController extends EntityController
{
    
    
     public function getTableDefinition()
     {   
          
         return new RepositoryTable();
     }

      public function getModel()
      {
         // echo "getmodel";
          return '\WpGet\Models\Repository';
      }
}
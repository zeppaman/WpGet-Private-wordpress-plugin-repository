<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\Package as Package;
use \WpGet\Controllers\EntityController as EntityController;
use \WpGet\db\PackageTable as PackageTable;

 class PackageController extends EntityController
{
    public function getTableDefinition()
    {   
         
        return new PackageTable();
    }

     public function getModel()
     {
        // echo "getmodel";
         return '\WpGet\Models\User';
     }
}
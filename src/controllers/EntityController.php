<?php
namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use  \WpGet\Models;

 abstract class EntityController extends DynamicController
{
     abstract public function getTableDefinition();
     abstract  public function getModel();
     
  
    function getAll($request, $response, $args) {   
        
        $td=$this->getTableDefinition();
        $columns=$td->getFieldDefinition();
        $model=$this->getModel();
        $columnNames=array_keys( $columns);
        $colString= implode(" ",$columnNames);
        $result=$model::all($columnNames);
      
        return $response->getBody()->write($result->toJson());
    }

    function getItem ($request, $response, $args) {
        $model=$this->getModel();
        $id = $args['id'];
        $dev = $model::find($id);
        $response->getBody()->write($dev->toJson());
        return $response;
    }


    function postItem($request, $response, $args) 
    {
        $model=$this->getModel();
        $data = $request->getParsedBody();
        $dev = new $model();

        $this->map($data,$dev);

        $dev->save();

        return $response->withStatus(201)->getBody()->write($dev->toJson());
    }

    function deleteItem($request, $response, $args) 
    {
        $model=$this->getModel();
       $id = $args['id'];
       $dev = $model::find($id);
       $dev->delete();
       return $response->withStatus(200);
    }

    
     function putItem($request, $response, $args) {
        $model=$this->getModel();
        $id = $args['id'];
        $data = $request->getParsedBody();
        $dev = $model::find($id);

        $this->map($data,$dev);

        $dev->save();

        return $response->getBody()->write($dev->toJson());
    }
   

    function map($source, &$dest)
    {
        $td=$this->getTableDefinition();
        $columns=$td->getFieldDefinition();
      
        foreach($columns as $column=>$type)
        {
            if(array_key_exists($column,$source))
            {
                $dest->{$column}=$source[$column];
            }
        }

      
    }
    
}
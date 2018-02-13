<?php
namespace WpGet\Controllers;

// fix for dependency sequence limitation
require_once "ProtectedController.php";



use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use  \WpGet\Models;
use WpGet\Controllers\ProtectedController;

 abstract class EntityController extends ProtectedController
{
     abstract public function getTableDefinition();
     abstract  public function getModel();
     
  
     function presave(&$entity,$data)
     {
        return;
     }
     function processInput(&$entity,&$data)
     {
        return;
     }

     function process(&$item)
     {
        return;
     }

    function getAll($request, $response, $args) {   
        $this->authorize();
        $td=$this->getTableDefinition();
        $columns=$td->getAllColumns();
        $model=$this->getModel();
        $columnNames=array_keys( $columns);
        $colString= implode(" ",$columnNames);
        $result=$model::all($columnNames);
       
        foreach($result as $item)
        {
            $this->process($item);
        }
      
        return $response->getBody()->write($result->toJson());
    }

    function getItem ($request, $response, $args) {
        $this->authorize();
        $model=$this->getModel();
        $id = $args['id'];
        $dev = $model::find($id);
        $this->process($dev);
        $response->getBody()->write($dev->toJson());
        return $response;
    }


    function postItem($request, $response, $args) 
    {
        $this->authorize();

      
        $model=$this->getModel();
        $data = $request->getParsedBody();
        $this-> processInput($dev,$data);
        $dev = new $model();
        //This should be managed by client as in REST standard, btw this may may help to simplify calls
        if(isset($data) && array_key_exists('id',$data))
        {
            return $response=$this->putItem($request, $response, $args);
        }

        $this->map($data,$dev);

        $this-> presave($dev,$data);
    
        $dev->save();

        return $response->withStatus(201)->getBody()->write($dev->toJson());
    }

    function deleteItem($request, $response, $args) 
    {
        $this->authorize();

        $model=$this->getModel();
       $id = $args['id'];
       $dev = $model::find($id);
       $dev->delete();
       return $response->withStatus(200);
    }

    
     function putItem($request, $response, $args) {
        $this->authorize();
        $model=$this->getModel();
        $data = $request->getParsedBody();
        $this-> processInput($dev,$data);
        $id = (\array_key_exists('id',$args))? $args['id']:$data["id"];
       
        $dev = $model::find($id);

        $this->map($data,$dev);
        $this-> presave($dev,$data);
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
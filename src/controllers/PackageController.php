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
         return '\WpGet\Models\Package';
     }


     function postPackage($request, $response, $args)
     {
         try
         {
             $user=$this->getUiUser();

             
 
             if(!$user->hasRole("admin"))
             {
                 return  $response->withStatus(403);
             }
 
             $data = $request->getParsedBody();
 
 
 
 
             $reposlug=$request->getQueryParam("reposlug");
             $name=$request->getQueryParam("name");
             $versionStr=$request->getQueryParam("version");
 
             if($pt->reposlug!=$reposlug)
             {
                 return  $response->withStatus(403);
             }
 
             if(!isset($reposlug) || strlen($reposlug)==0)
             {
                 $reposlug="default";
             }
             if(!isset($name) || strlen($name)==0)
             {
                 throw new Exception("name missing");
             }
 
             if(!isset($versionStr) || strlen($versionStr)==0)
             {
                throw new Exception("version missing");
             }
 
             $files = $request->getUploadedFiles();
             if(isset($files) && sizeof($files)>0)
             {
                 throw new \Exception("Package already present.");
             }
             $uploadedFile=$files[0];
 
             
             //TODO: OPEN PACKAGE AND READ METADATA
 
             $pk= new Package();
             $pk->name=$name;
             $pk->reposlug=$reposlug;
             $pk->version=$version;
             // $pk->minor=$minor;
             // $pk->major=$major;
             // $pk->build=$build;
             $pk->setVersionFromString($version);
             $pk->relativepath=$repoPath;
 
             $pk->save();
 
        
             $pm->addPackage($pm,$uploadedFile);
         }
         catch(\Exception $e)
         {
             return $response=  $response->withStatus(500)->body()->write($e->getMessage());
         }
 
        
 
         return $response->getBody()->write($pk->toJson());
 
     }
}
<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Controllers\ProtectedController as ProtectedController;
use \WpGet\db\Package as Package;
use \Illuminate\Database\Eloquent\Model as Model;
use \WpGet\Models\PublishToken;
use \WpGet\Utils\Util as Util;

 class CatalogController extends ProtectedController
{
    function getStatus($request, $response, $args)
    {   
        $charset = $app->request->headers->get('ACCEPT_CHARSET');   
        $response->getBody()->write( "OK");
    }


    function postPackage($request, $response, $args)
    {
        $user=$this->getServiceUser($request);
        $pt= PublishToken::where('readtoken', '=', $user->token)->get();

        if(!isset($user) || !isset($pt) ) 
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
            return $response=  $response->withStatus(500)->body()->write("name missing");
        }

        if(!isset($versionStr) || strlen($versionStr)==0)
        {
            return $response=  $response->withStatus(500)->body()->write("version missing");
        }

        if (!preg_match("/(\d{1,})\.(\d{1,}).(\d{1,})", $versionStr,$matches)) {
        {
            return $response=  $response->withStatus(500)->body()->write("version missing");
        }
        $major=$matches[0];
        $minor=$matches[1];
        $build=$matches[2];

         $packages=Package::where('version', '=', $versionStr)
                        ->where('name', '=', $name)
                        ->where('reposlug', '=', $reposlug)
                        ->get();
        
        if(isset($packages) && sizeof($packages)>0)
        {
            return $response=  $response->withStatus(500)->body()->write("package already exists");
        }

        $files = $request->getUploadedFiles();
        if(isset($files) && sizeof($files)>0)
        {
            return $response=  $response->withStatus(500)->body()->write("files missing or duplicated");
        }
        $uploadedFile=$files[0];
        $tempdir=$this->container["settings"]["appsettings"]["tempdir"];
        $storagedir=$this->container["settings"]["appsettings"]["storagedir"];
        $repoPath="$reposlug/$name/$versionStr/$name_$versionStr.zip";
        $tmpPath=Util::generateRandomString(10).".zip";
        $tmpFullPath=$tempdir . DIRECTORY_SEPARATOR . $tmpPath;
        $repoFullPath=$storagedir . DIRECTORY_SEPARATOR . $repoPath;
        $uploadedFile->moveTo($tempdir . DIRECTORY_SEPARATOR . $tmpPath);
        //TODO: OPEN PACKAGE AND READ METADATA 
        
        $pk= new Package();
        $pk->name=$name;
        $pk->reposlug=$reposlug;
        $pk->version=$version;
        $pk->minor=$minor;
        $pk->major=$major;
        $pk->build=$build;
        $pk->relativepath=$repoPath;

        $pk->save();
        
        rename($tmpFullPath,$repoFullPath);

        return $response->getBody()->write($pk->toJson());

    }

    function getPackageAllVersions($request, $response, $args)
    {
        $user=$this->getServiceUser($request);
        $pt= PublishToken::where('readtoken', '=', $user->token)->get();

        if(!isset($user) || !isset($pt) ) 
        {
            return  $response->withStatus(403);  
        }

        $data = $request->getParsedBody();

     
       

        $reposlug=$data["reposlug"];
        $name=$data["name"];
        $versionStr=$data["version"];

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
            return $response=  $response->withStatus(500)->body()->write("name missing");
        }

        if(!isset($versionStr) || strlen($versionStr)==0)
        {
            return $response=  $response->withStatus(500)->body()->write("version missing");
        }

         $packages=Package::where('version', '=', $versionStr)
                        ->where('name', '=', $name)
                        ->where('reposlug', '=', $reposlug)
                        ->get();
        if(!isset($packages) )
        {
            return $response=  $response->withStatus(500)->body()->write("package not found or duplicateds");
        }
         return  $response->body()->write($packages->toJson()); 
    }

    function getPackage($request, $response, $args)
    {
            $user=$this->getServiceUser($request);
            $pt= PublishToken::where('readtoken', '=', $user->token)->get();

            if(!isset($user) || !isset($pt) ) 
            {
                return  $response->withStatus(403);  
            }

            $data = $request->getParsedBody();

         
           

            $reposlug=$data["reposlug"];
            $name=$data["name"];
            $versionStr=$data["version"];

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
                return $response=  $response->withStatus(500)->body()->write("name missing");
            }

            if(!isset($versionStr) || strlen($versionStr)==0)
            {
                return $response=  $response->withStatus(500)->body()->write("version missing");
            }

             $packages=Package::where('version', '=', $versionStr)
                            ->where('name', '=', $name)
                            ->where('reposlug', '=', $reposlug)
                            ->get();
            if(!isset($packages) || sizeof($packages)!=1)
            {
                return $response=  $response->withStatus(500)->body()->write("package not found or duplicateds");
            }
             return  $response->body()->write($packages[0]->toJson());  
    }
   
}
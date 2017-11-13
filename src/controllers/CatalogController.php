<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Controllers\ProtectedController as ProtectedController;

use \Illuminate\Database\Eloquent\Model as Model;
use \WpGet\Models\PublishToken as PublishToken;
use \WpGet\Utils\Util as Util;
use \WpGet\Utils\PackageManager;
use WpGet\Models\Package;

 class CatalogController extends ProtectedController
{
    private $pm;

    function __construct($container)
    {
        $this->pm= new PackageManager($container);
        parent::__construct($container);
    }
    function getStatus($request, $response, $args)
    {
        
        $response->getBody()->write( "OK");
    }


    function postPackage($request, $response, $args)
    {
        try
        {
            $this->logger->info("Post Package");
            
            $user=$this->getServiceUser($request);
            $this->logger->debug( json_encode( $user));
            $pt= PublishToken::where('writetoken', '=', $user->token)->get()[0];
            $this->logger->debug( $pt->toJson());

            if(!isset($user) || !isset($pt) )
            {
                $this->logger->error( "user not set or token not set");
                return  $response->withStatus(403);
            }

            $data = $request->getParsedBody();

            $this->logger->debug( print_r($data,TRUE));

            $reposlug=$data["reposlug"];
            $name=$data["name"];
            $versionStr=$data["version"];

            if(!isset($reposlug) || strlen($reposlug)==0)
            {
                $reposlug="default";
            }

            $this->logger->error( "Parsed input  reposlug:$reposlug  name:$name versionStr:$versionStr");

            
            if($pt->reposlug!=$reposlug)
            {
                $this->logger->error( "reposlug not matching");
                return $response=  $response->withStatus(500)->body()->write("name missing");
            }

           
            if(!isset($name) || strlen($name)==0)
            {
                $this->logger->error( "name missing");
                return $response=  $response->withStatus(500)->body()->write("name missing");
            }

            if(!isset($versionStr) || strlen($versionStr)==0)
            {
                $this->logger->error("version missing");
                return $response=  $response->withStatus(500)->body()->write("version missing");
            }

            
            $uploadedFile = $request->getUploadedFiles();
            $this->logger->info("FILE:".print_r($uploadedFile ,TRUE));
            

            
           
            $this->logger->info(" //TODO: OPEN PACKAGE AND READ METADATA");
            $pk= new Package();
            $this->logger->info("filling package data");
            $pk->name=$name;
            $pk->reposlug=$reposlug;
           
            $pk->setVersionFromString($versionStr);
            
            $this->logger->debug( "saving package:".$pk->toJSon());
            
           

       
            $pk=$this->pm->addPackage($pk,$uploadedFile);
           
        }
        catch(\Exception $e)
        {
            $this->logger->error($e);
            return $response=  $response->withStatus(500)->getBody()->write($e->getMessage());
        }

       

        return $response->getBody()->write($pk->toJson());

    }

    function getPackageAllVersions($request, $response, $args)
    {
        try
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

            
            $packages=$pm->getPackages($name,$reposlug);
            if(!isset($packages) )
            {
                return $response=  $response->withStatus(500)->body()->write("package not found or duplicateds");
            }

            
            return  $response->body()->write($packages->toJson());

        }
        catch(\Exception $e)
        {
            return $response=  $response->withStatus(500)->body()->write($e->getMessage());
        }
    }

    function getPackage($request, $response, $args)
    {

        try
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

            $package=$pm->getPackages($versionStr,$name,$reposlug);

            
             return  $response->body()->write($package->toJson());

        }
        catch(\Exception $e)
        {
            return $response=  $response->withStatus(500)->body()->write($e->getMessage());
        }
    }

}
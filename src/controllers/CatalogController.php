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
    private $uisettings;

     function  __construct($container)
    {
        $this->pm= new PackageManager($container);
        $this->uisettings=$container['settings']['ui'];
        
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

            $data = $request->getParsedBody();
            
            $this->logger->debug( print_r($data,TRUE));

            $reposlug=isset($data["reposlug"])?$data["reposlug"]:null ;
            $name=isset($data["name"])? $data["name"]:null;

            $versionStr=isset($data["version"])?$data["version"]:null;
           
            if(!isset($reposlug) || strlen($reposlug)==0)
            {
                $reposlug="default";
            }

            $this->logger->info( "Parsed input  reposlug:$reposlug  name:$name versionStr:$versionStr");

            // user control
            
            $user=$this->getUser();
            if(!isset($user))
            {
                //user not authentication or wrong code
                throw new \Exception("user not authentication or wrong code");
            }
            else if($user->type=="SERVICE")
            {
                $this->logger->debug( json_encode( $user));
                $pt= PublishToken::where('writetoken', '=', $user->token)->get()[0];
                $this->logger->debug( $pt->toJson());

                if(!isset($user) || !isset($pt) )
                {
                    $this->logger->error( "user not set or token not set");
                    return  $response->withStatus(403);
                }

                if($pt->reposlug!=$reposlug)
                {
                    $this->logger->error( "reposlug not matching ");
                    return $response=  $response->withStatus(500)->body()->write("name missing");
                }

            }
            else if($user->type=="UI")
            {
                  //TODO: in future check if user has control over this repo
            }
            else
            {
              throw new \Exception("Unable to use user type undefined");
            }
           
            if(!isset($name) || strlen($name)==0)
            {
                $this->logger->info( "name missing, try to use YAML definition");
                //return $response=  $response->withStatus(500)->body()->write("name missing");
            }

           

            if($versionStr=="next")
            {
              
               $lastversion= $this->pm->getLastestVersion($name,$reposlug);
                if($lastversion==null) 
                {
                    $versionStr="1.0.0";             
                }
                else
                {
                    $versionStr=($lastversion->major+1).".".$lastversion->minor.".".$lastversion->build;
                }
            }

            if(!isset($versionStr) || strlen($versionStr)==0)
            {
                $this->logger->info("version missing");
               // return $response=  $response->withStatus(500)->body()->write("version missing");
            }

            
            $uploadedFile = $request->getUploadedFiles();
            $this->logger->info("FILE:".print_r($uploadedFile ,TRUE));
            
           
            
           
            $this->logger->info(" //TODO: OPEN PACKAGE AND READ METADATA");
            $pk= new Package();
            $this->logger->info("filling package data");
            $pk->name=$name;
            $pk->reposlug=$reposlug;
            if(isset($versionStr))
            {
                $pk->setVersionFromString($versionStr);
            }
            $this->logger->debug( "saving package:".$pk->toJSon());
            
           

       
            $pk=$this->pm->addPackage($pk,$uploadedFile);
           
        }
        catch(\Exception $e)
        {
            $this->logger->error($e);
            return $response=  $response->withStatus(500)->getBody()->write($e->getMessage());
        }

       
        if($user->type=="UI")
        {
            return $response->withStatus(302)->withHeader('Location', $this->uisettings["url"].'admin/packages');
        }

        return $response->getBody()->write($pk->toJson());

    }

    function getPackageAllVersions($request, $response, $args)
    {
        try
        {
            $data = $request->getQueryParams();        
            $this->logger->debug("DATA:".print_r($data,TRUE));

            $user=$this->getServiceUser($request);
            $pt= PublishToken::where('readtoken', '=', $user->token)->get();

            if(!isset($user) || !isset($pt) )
            {
                return  $response->withStatus(403);
            }

            $reposlug=$data["reposlug"];
            $name=$data["name"];
           // $versionStr=$data["version"];

            // if($pt->reposlug!=$reposlug)
            // {
            //     return  $response->withStatus(403);
            // }

            if(!isset($reposlug) || strlen($reposlug)==0)
            {
                $reposlug="default";
            }
            if(!isset($name) || strlen($name)==0)
            {
                return $response=  $response->withStatus(500)->getBody()->write("name missing");
            }

            
            $packages=$this->pm->getPackages($name,$reposlug);
            if(!isset($packages) )
            {
                return $response=  $response->withStatus(500)->getBody()->write("package not found or duplicateds");
            }

            
            return  $response->getBody()->write($packages->toJson());

        }
        catch(\Exception $e)
        {
            return $response=  $response->withStatus(500)->getBody()->write($e->getMessage());
        }
    }

    function getPackage($request, $response, $args)
    {

        try
        {
            $data = $request->getQueryParams();
            $this->logger->debug("DATA:".print_r($data,TRUE));

            $user=$this->getServiceUser($request);
            $pt= PublishToken::where('readtoken', '=', $user->token)->get();

            if(!isset($user) || !isset($pt) )
            {
                return  $response->withStatus(403);
            }

          



            $reposlug=$data["reposlug"];
            $name=$data["name"];
            $versionStr="";
            if(array_key_exists('version',$data))
            {
                $versionStr=$data["version"];
            }

            // if($pt->reposlug!=$reposlug)
            // {
            //     return  $response->withStatus(403);
            // }

            if(!isset($reposlug) || strlen($reposlug)==0)
            {
                $reposlug="default";
            }
            if(!isset($name) || strlen($name)==0)
            {
                return $response=  $response->withStatus(500)->getBody()->write("name missing");
            }

            if(!isset($versionStr) || strlen($versionStr)==0)
            {
               $package= $this->pm->getLastestVersion($name);
            }
            else
            {

                $package=$this->pm->getPackage($versionStr,$name,$reposlug);
            }
            
             return  $response->getBody()->write($package->toJson());

        }
        catch(\Exception $e)
        {
            return $response=  $response->withStatus(500)->getBody()->write($e->getMessage());
        }
    }

}
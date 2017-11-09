<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Controllers\ProtectedController as ProtectedController;
use \WpGet\db\Package as Package;
use \Illuminate\Database\Eloquent\Model as Model;
use \WpGet\Models\PublishToken;
use \WpGet\Utils\Util as Util;
use \WpGet\Utils\PackageManager;

 class CatalogController extends ProtectedController
{
    private $pm;

    function __construct()
    {
        $this->pm= new PackageManager();
    }
    function getStatus($request, $response, $args)
    {
        $charset = $app->request->headers->get('ACCEPT_CHARSET');
        $response->getBody()->write( "OK");
    }


    function postPackage($request, $response, $args)
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

       
            $pm->addPackage($pm,uploadedFile);
        }
        catch(\Exception $e)
        {
            return $response=  $response->withStatus(500)->body()->write($e->getMessage());
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
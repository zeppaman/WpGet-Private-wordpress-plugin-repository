<?php
namespace WpGet\utils;

use \WpGet\Models\Package;



class PackageManager
{
    public $container;
    public $logger;

    public function __construct($container)
    {
        $this->container=$container;
        $this->logger=$container["logger"];
        
    }

    public function addPackage(Package $pk,$uploadedFile)
    {

        $tempdir=$this->container["settings"]["appsettings"]["tempdir"];
        $storagedir=$this->container["settings"]["appsettings"]["storagedir"];
        $packages=Package::where('version', '=', $pk->version)
        ->where('name', '=', $pk->name)
        ->where('reposlug', '=', $pk->reposlug)
        ->get();

        if(isset($packages) && sizeof($packages)>0)
        {
          throw new \Exception("Unable to overwirte a package. please add a new version.") ;
        }

       
    
        $repoPath=$pk->reposlug."/".$pk->name."/".$pk->version."/".$pk->name."_".$pk->version.".zip";
        $pk->relativepath=$repoPath;
        $tmpPath=Util::generateRandomString(10).".zip";
        $tmpFullPath=$tempdir . DIRECTORY_SEPARATOR . $tmpPath;
        $repoFullPath=$storagedir . DIRECTORY_SEPARATOR . $repoPath;
        $this->logger->info("uploading to $repoFullPath");
        $uploadedFile["file"]->moveTo($tempdir . DIRECTORY_SEPARATOR . $tmpPath);
        $pk->save();
        rename($tmpFullPath,$repoFullPath);
        return $pk;
    }

    public function getPackages($versionStr, $name,$reposlug)
    {
        return Package::where('version', '=', $versionStr)
        ->where('name', '=', $name)
        ->where('reposlug', '=', $reposlug)
        ->get();
    }

    public function getPackage( $name,$reposlug)
    {
        return Package::where('name', '=', $name)
        ->where('reposlug', '=', $reposlug)
        ->get();
    }
}
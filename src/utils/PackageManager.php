<?php
namespace WpGet\utils;

use \WpGet\Models\Package;



class PackageManager
{
    public $container;
    public $logger;
    public $replacements;

    public function __construct($container)
    {
        $this->container=$container;
        $this->logger=$container["logger"];

        $this->replacements=array(); 
        $this->replacements["root"]=realpath("../../");
        
    }

    function  doReplacements( &$dir)
    {
        foreach($this->replacements  as $key=>$value)
        {
            $dir=str_replace("{".$key."}",$value,$dir);
        }
    }   

    function ensureDirForPath($path)
    {
        $dir=dirname($path);
        if(!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
    }

    public function addPackage(Package $pk,$uploadedFile)
    {
      

        $tempdir=$this->container["settings"]["packageManager"]["tempdir"];
        $storagedir=$this->container["settings"]["packageManager"]["storagedir"];


        
        $this->doReplacements( $tempdir);
        $this->doReplacements( $storagedir);
        $this->logger->debug("Replaced path".$tempdir." ". $storagedir);
        $packages=Package::where('version', '=', $pk->version)
        ->where('name', '=', $pk->name)
        ->where('reposlug', '=', $pk->reposlug)
        ->get();

        // if(isset($packages) && sizeof($packages)>0)
        // {
        //   throw new \Exception("Unable to overwirte a package. please add a new version.") ;
        // }

    
    
        $repoPath=$pk->reposlug."/".$pk->name."/".$pk->version."/".$pk->name."_".$pk->version.".zip";
        $this->logger->info("Repo path:".$repoPath);
        $pk->relativepath=$repoPath;
        $tmpPath=Util::generateRandomString(10).".zip";
        $tmpFullPath=$tempdir . DIRECTORY_SEPARATOR . $tmpPath;
        $repoFullPath=$storagedir . DIRECTORY_SEPARATOR . $repoPath;

        $this->ensureDirForPath($tmpFullPath);
        $this->ensureDirForPath($repoFullPath);

        $this->logger->info("uploading to $repoFullPath ($tmpFullPath)");
        $uploadedFile["file"]->moveTo($tmpFullPath);
        $pk->save();
        rename($tmpFullPath,$repoFullPath);
        return $pk;
    }

    public function getPackage($versionStr, $name,$reposlug)
    {

        return Package::where('version', '=', $versionStr)
        ->where('name', '=', $name)
        ->where('reposlug', '=', $reposlug)
        ->get()[0];
        
    }

    public function getPackages( $name,$reposlug)
    {
        
        return Package::where('name', '=', $name)
        ->where('reposlug', '=', $reposlug)
        ->get();
    }

    public function getLastestVersion($name,$reposlug)
    {
       $result= Package::where('name','=',$name) 
        ->where('reposlug', '=', $reposlug)
        ->orderBy('major', 'desc')
        ->orderBy('minor', 'desc')
        ->orderBy('build', 'desc')
        ->take(1)
        ->get();
        if(isset($result)&& sizeof($result)>0)
        {
            return $result[0];
        }
        return null;
    }
}
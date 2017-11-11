<?php
namespace WpGet\utils;

use \WpGet\db\Package;

class PackageManager
{
    public function addPackage(Package $pk,$uploadedFile)
    {
        $packages=Package::where('version', '=', $versionStr)
        ->where('name', '=', $name)
        ->where('reposlug', '=', $reposlug)
        ->get();

        if(isset($packages) && sizeof($packages)>0)
        {
           
        }

       
        $tempdir=$this->container["settings"]["appsettings"]["tempdir"];
        $storagedir=$this->container["settings"]["appsettings"]["storagedir"];
        $repoPath="$reposlug/$name/$versionStr/$name_$versionStr.zip";
        $tmpPath=Util::generateRandomString(10).".zip";
        $tmpFullPath=$tempdir . DIRECTORY_SEPARATOR . $tmpPath;
        $repoFullPath=$storagedir . DIRECTORY_SEPARATOR . $repoPath;
        $uploadedFile->moveTo($tempdir . DIRECTORY_SEPARATOR . $tmpPath);
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
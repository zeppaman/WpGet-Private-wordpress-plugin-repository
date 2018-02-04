<?php
namespace WpGet\utils;

use \WpGet\Models\Package;
use \ZipArchive;
use Symfony\Component\Yaml\Yaml as YamlParser;


class PackageManager
{
    public $container;
    public $logger;
    public $replacements;
    public $tempDir;
    public $storageDir;

    public function __construct($container)
    {
        $this->container=$container;
        $this->logger=$container["logger"];

        $this->replacements=array(); 
        $this->replacements["root"]=realpath("../../");

         $this->tempDir=$this->container["settings"]["packageManager"]["tempdir"];
         $this->storageDir=$this->container["settings"]["packageManager"]["storagedir"];


        
        $this->doReplacements(  $this->tempDir);
        $this->doReplacements(  $this->storageDir);
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
        $this->ensureDir($dir);
    }
    function ensureDir($dir)
    {
        if(!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
    }

    public function addPackage(Package $pk,$uploadedFile)
    {
        $tmpPath=Util::generateRandomString(10).".zip";
        $tmpFullPath=$this->tempDir . DIRECTORY_SEPARATOR . $tmpPath;
        $this->ensureDirForPath($tmpFullPath);
        $uploadedFile["file"]->moveTo($tmpFullPath);        
        $pk=$this->loadFromYml($pk,$tmpFullPath);

       
        $this->logger->debug("Replaced path".$this->tempDir." ". $this->storageDir);
        $packages=Package::where('version', '=', $pk->version)
        ->where('name', '=', $pk->name)
        ->where('reposlug', '=', $pk->reposlug)
        ->get();

        if(isset($packages) && sizeof($packages)>0)
        {
          throw new \Exception("Unable to overwirte a package. please add a new version.") ;
        }

    
    
        $repoPath=$pk->reposlug."/".$pk->name."/".$pk->version."/".$pk->name."_".$pk->version.".zip";        
        $repoFullPath=$this->storageDir . DIRECTORY_SEPARATOR . $repoPath;
        $this->logger->info("Repo path:".$repoPath);
        $pk->relativepath=$repoPath;
        

        
        $this->ensureDirForPath($repoFullPath);

        $this->logger->info("uploading to $repoFullPath ($tmpFullPath)");
       

        if(!$pk->description)
        {
            $pk->description="No description provided for".$pk->name;
        }
        if(!$pk->name || sizeof($pk->name)==0)
        {
            throw new \Exception("Unable to upload a package without a name.");
        }
      
        $pk->save();
        rename($tmpFullPath,$repoFullPath);
        return $pk;
    }

    public function loadFromYml($pk,$packPath)
    {
        $zip = new \ZipArchive;
        if ($zip->open($packPath, ZipArchive::CREATE) !== TRUE) {
            return $pk;
        }

       $files= $zip->locateName('.wpget.yml', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR) . "\n";
       
       if($files && $files>-1)
       {
            $contents=$zip->getFromIndex($files[0]);
            $manifest=YamlParser::parse( $contents);
            if(isset($manifest["description"])) $pk->description=$manifest["description"];
            if(isset($manifest["changelog"])) $pk->changelog=$manifest["changelog"];
            if(isset($manifest["name"])) $pk->name=trim($manifest["name"]);
            if(isset($manifest["version"]))   
            {

                $pk->setVersionFromString(trim($manifest["version"]));
            }
            
       }
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
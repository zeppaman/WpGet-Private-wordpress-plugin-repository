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
        $pk = $this->loadFromYml($pk,$tmpFullPath);
        

        
        $this->logger->debug("Replaced path: ".$this->tempDir." ". $this->storageDir);
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
            $pk->description="No description provided for: ".$pk->name;
        }
        if(!$pk->name || strlen($pk->name)==0)
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
        if ($zip->open($packPath, ZipArchive::CREATE) !== TRUE)
        {
            return $pk;
        }

       $files = $zip->locateName('.wpget.yml', ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR) ;
       
       if($files && $files>-1)
       {
           
            $contents = $zip->getFromIndex($files);
            $manifest=YamlParser::parse( $contents);

         
            // TODO: make this load dinamically
            // TODO: sanitize by field type )

            if(isset($manifest["name"])) $pk->name=trim($manifest["name"]);
            if(isset($manifest["version"]))   
            {
                $pk->setVersionFromString(trim($manifest["version"]));
            }

            if(isset($manifest["added"])) $pk->added=trim($manifest["added"]);
            if(isset($manifest["upgrade_notice"])) $pk->upgrade_notice=trim($manifest["upgrade_notice"]);
            if(isset($manifest["tested"])) $pk->tested=trim($manifest["tested"]);

            if(isset($manifest["homepage"])) $pk->homepage=trim($manifest["homepage"]);
            if(isset($manifest["author"])) $pk->author=trim($manifest["author"]);
            if(isset($manifest["author_profile"])) $pk->author_profile=trim($manifest["author_profile"]);

            if(isset($manifest["requires"])) $pk->requires=trim($manifest["requires"]);
            if(isset($manifest["requires_php"])) $pk->requires_php=trim($manifest["requires_php"]);

            // section 

            if(isset($manifest["description"])) $pk->description=$manifest["description"];
            if(isset($manifest["installation"])) $pk->installation=$manifest["installation"];
            if(isset($manifest["faq"])) $pk->faq=$manifest["faq"];
            if(isset($manifest["changelog"])) $pk->changelog=$manifest["changelog"];
            if(isset($manifest["old_version"])) $pk->old_version=$manifest["old_version"];

            // banner
            if(isset($manifest["banners_low"])) $pk->banners_low=trim($manifest["banners_low"]);
            if(isset($manifest["banners_high"])) $pk->banners_high=trim($manifest["banners_high"]);

            // icons
            if(isset($manifest["icons_1x"])) $pk->icons_1x=trim($manifest["icons_1x"]);
            if(isset($manifest["icons_2x"])) $pk->icons_2x=trim($manifest["icons_2x"]);
            if(isset($manifest["icons_default"])) $pk->icons_default=trim($manifest["icons_default"]);

            
            
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
<?php
namespace WpGet\utils;

class DependencyManager
{
    
    public $baseFolder;

    public function __construct($_baseFolder)
    {
       $this->baseFolder=$_baseFolder;
    }

    public  function requireOnceFolders($relativeFolders)
    {
        foreach($relativeFolders as $folder)
        {
            $this->requireOnceFolder($folder);
        }
        
    }
    public  function requireOnceFolder($relativeFolder)
    {
        
      $folder=$this->resolvePath($relativeFolder);
     
        foreach (glob("$folder/*.php") as $filename) {
           
            require_once $filename;
        }
    }

    public function resolvePath($relativeFolderOrFile)
    {
        $folder=$this->baseFolder . DIRECTORY_SEPARATOR . $relativeFolderOrFile;
        return $folder;
    }
}
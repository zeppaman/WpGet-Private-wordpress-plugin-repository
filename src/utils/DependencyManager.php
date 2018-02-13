<?php
namespace WpGet\utils;
use \WpGet\db\UpdateManager as UpdateManager;

use \WpGet\db\TableBase as TableBase;
use \WpGet\db\RepositoryTable as RepositoryTable;
use \WpGet\db\UsersTable as UsersTable;
use \WpGet\db\PackageTable as PackageTable;
use \WpGet\db\PublishTokenTable as PublishTokenTable;
use \WpGet\Models\User as User;

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

    public function upgradeDB($dbSettings)
    {
        //check for installation
        $um= new UpdateManager($dbSettings);

        //TODO: make dinamic
        $um->addTable(new RepositoryTable());
        $um->addTable(new PublishTokenTable());
        $um->addTable(new UsersTable());
        $um->addTable(new PackageTable());
        $um->run();

        //create default user on first installation with default password
        $userCount=User::all()->count();
        if($userCount==0)
        {
            $u = new User();
            $u->username='admin';
            $u->password=hash('sha512','admin');
            $u->save();
        }
        
    }

    
}
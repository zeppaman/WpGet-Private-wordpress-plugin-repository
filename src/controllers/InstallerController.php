<?php

namespace WpGet\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \WpGet\Models\User as User;
use \Monolog\Logger as Logger;
use \WpGet\Utils\PackageManager;

 class InstallerController
{
    protected $logger;
    protected $container;
    protected $pm;
    protected $dm;

    public $writablePaths;
    
       public function __construct( $container)
       {
          
           $this->container=$container;
           $this->logger = $container["logger"];
           $this->pm= new PackageManager($container);
           $this->writablePaths= array(
               realpath("../../")."/web/ui/assets" =>" during installation, must be changed after that.",
               realpath("../../")."/config" =>" during installation, must be changed after that.",
               $this->pm->tempDir =>"temporary forlder have to be writable",
               $this->pm->storageDir =>"temporary forlder have to be writable",
           );

          $this->dm= $this->container['dm'];
       }

   
    public function __invoke($request, $response, $args)  
    {
        $output="";
        try
        {
         $fileErrors=false;
              foreach( $this->writablePaths as $path => $error)
              {
              $output.=("<br> CHECKING : ".$path);
                try
                {
                    $this->pm->ensureDir($path);
                    if(!is_writable(str_replace("//","/", $path."/")))
                    {
                         $output.=("<span style='color:red'>&nbsp;&nbsp;&nbsp;> : Folder not writable</span>");
                        $fileErrors=true;
                    }
                }
                catch(\Exception $err)
                {
                    $output.=("<span style='color:red'>&nbsp;&nbsp;&nbsp;> : Unable to create folder</span>");
                    $fileErrors=true;
                }
                

              }
              if( $fileErrors)
              {
                $output.=("<h2> style='color:red'>Missing file permission. Installation cannot continue</h2>");
                $fileErrors=true;
                return $response->getBody()->write($output);
              }

              $lockFile='web/ui/assets/settings.json';//deleted after build
              $configPath=$this->dm->resolvePath($lockFile);

              if(true)
              {
                // TODO: check for file or upgrade table
                $output.=("<br>Checking for table data (TODO)");
               
               $output.=("<br>Start DB Upgrade");
                $this->dm->upgradeDB( $this->container['settings']['db']);
               $output.=("<br>Generating JSON settings");      
               
                $output.=("<br>Installation completed");
              }
              else
              {
                $output.=( "<br>INSTALLATION ALREADY DONE. NOTHING TODO.");
              }
        
              //Write JSON config
              $jsonSettings=$this->dm->resolvePath('web/ui/assets/settings.json');

              $baseUrl=  str_replace('/api/install','/' ,$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
              $fullUrl= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
              $host=substr($fullUrl,0,strpos($fullUrl,"/"));

              $defaulSettings= array(
                'apiHost' =>  $baseUrl,
                'baseHref' =>  str_replace($host,'' ,$baseUrl).'ui/',
                //'fullUrl' => $fullUrl,
                'host' => $host,
                );
            
           
              
              $uisettings= $this->container['settings']['ui'];
            
              $uisettings=array_replace ( $defaulSettings,$uisettings);
            
           

              try
              {
                file_put_contents($jsonSettings, json_encode($uisettings));
                file_put_contents($configPath, json_encode($uisettings));
              }
              catch ( \Exception $er12) {}


            }
            catch(\Exception $err)
            {
                $output.=$err->getMessage();
            }
              return $response->getBody()->write($output);
     }
}
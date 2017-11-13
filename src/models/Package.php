<?php  
namespace   WpGet\Models;
class Package extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'package';


  public function setVersionFromString($versionStr)
  {
      if (!preg_match("/(\d{1,})\.(\d{1,}).(\d{1,})/", $versionStr,$matches)) 
          {
             throw new \Exception("unable to parse version".$versionStr);
          }
          $this->version=$versionStr;
          $this->major=$matches[1];
          $this->minor=$matches[2];
          $this->build=$matches[3];
  }
}
<?php  
namespace   WpGet\Models;
class Package extends \Illuminate\Database\Eloquent\Model {  
  protected $table = 'package';


  public function setVersionFromString($versionStr)
  {
      if (!preg_match("/(\d{1,})\.(\d{1,}).(\d{1,})", $versionStr,$matches)) 
          {
              return $response=  $response->withStatus(500)->body()->write("version missing");
          }
          $this->major=$matches[0];
          $this->minor=$matches[1];
          $this->build=$matches[2];
  }
}
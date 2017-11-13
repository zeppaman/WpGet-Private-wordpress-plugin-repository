<?php
namespace WpGet\db;

use Illuminate\Database\Capsule\Manager as Manager;
use \Illuminate\Support\Facades\Schema as Schema;
use \Illuminate\Database\Schema\Builder as Builder;

class UpdateManager 
{
    private $connection;

    private $tables=array();

    function __construct($conn)
    {           
       
       $this->connection=$conn; 
           
    }

    function addTable(TableBase $table)
    {
       // echo "added table ".$table->getTableName();
        array_push($this->tables,$table);
       //print_r( $this->tables);
        return $this;
    }

    function run()
    {
        $capsule = new Manager();
        $capsule->addConnection($this->connection);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();  
        $schema=$capsule->schema();
        //print_r($capsule->schema());
      
        //print_r($this->tables);

        
      
        foreach($this->tables as $tableDef)
        {
           // echo "table:".$tableDef->getTableName();
            $tablename=$tableDef->getTableName();
            $fields=$tableDef->getAllColumns();
           // print_r( $fields);
        if (!$schema->hasTable($tablename)) 
        {
            
                       // echo "missing table $tablename, create";
                        $schema->create($tablename, function ( $table) use ($schema,$tablename,$fields) {
                         //   echo "creating callback"   ;  
                           $this->provideFields($schema,$tablename,$table,$fields);
                        });
        }
        else
        {
            //TODO: solve update issue.
            
                //    $schema->table($tablename, function ( $table) use ($schema,$tablename,$fields) {
                   
                //         $this->provideFields($schema,$tablename,$table,$fields);
                //     });
        }

      }
    }
            
                function provideFields($schema,$tablename,$table,$fields)
                {
                   
                    foreach($fields as $fieldname=>$fieldfunc)
                    {
                        echo "<br>$tablename $fieldname=>$fieldfunc";
                        if(!$schema->hasColumn($tablename,$fieldname))
                        {
                           echo "missing $fieldname => $fieldfunc";
                            $table->$fieldfunc($fieldname);
                        }
                        else
                        {
                          //  echo "has";
                            $table->$fieldfunc($fieldname)->change();
                        }
                    }
                }
}
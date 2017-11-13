<?php
namespace WpGet\db;

class PackageTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'reposlug' =>'string',
            'name' =>'string',
            'description'=>'string',
            'major'=>'integer',
            'minor'=>'integer',
            'build' =>'integer',
            'version' =>'string',            
            'relativepath' =>'string'
        );
    }

    
    public function getTableName()
    {
        return "package";
    }
}
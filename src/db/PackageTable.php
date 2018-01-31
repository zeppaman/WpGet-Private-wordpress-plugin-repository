<?php
namespace WpGet\db;

class PackageTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'reposlug' =>'string',
            'name' =>'string',
            'description'=>'text',
            'major'=>'integer',
            'minor'=>'integer',
            'build' =>'integer',
            'version' =>'string',            
            'relativepath' =>'string',
            'changelog' =>'text'
        );
    }

    
    public function getTableName()
    {
        return "package";
    }
}
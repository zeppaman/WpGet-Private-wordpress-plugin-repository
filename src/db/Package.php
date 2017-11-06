<?php
namespace WpGet\db;

class Package extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'reposlug' =>'string',
            'name' =>'string',
            'description'=>'string',
            'major'=>'int',
            'minor'=>'int',
            'build' =>'string',
            'version' =>'string',            
            'relativepath' =>'string'
        );
    }

    
    public function getTableName()
    {
        return "package";
    }
}
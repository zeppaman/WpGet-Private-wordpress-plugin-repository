<?php
namespace WpGet\db;

class PublishTokenTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'reposlug' =>'string',
            'readtoken' =>'string',
            'writetoken'=>'string',
            'description'=>'text',
        );
    }

    
    public function getTableName()
    {
        return "publishtoken";
    }
}
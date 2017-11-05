<?php
namespace WpGet\db;

class RepositoryTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'name' =>'string',
            'description' =>'text',
            'reposlug'=>'string',
        );
    }

    
    public function getTableName()
    {
        return "repository";
    }
}
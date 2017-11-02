<?php
namespace WpGet\db;

class RepositoryTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'name' =>'string',
            'description' =>'text',
            'slug'=>'string',
        );
    }

    
    public function getTableName()
    {
        return "repository";
    }
}
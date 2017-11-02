<?php
namespace WpGet\db;

class PublishTokenTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'repo_slug' =>'string',
            'read_token' =>'string',
            'write_token'=>'string',
            'desctiption'=>'text',
        );
    }

    
    public function getTableName()
    {
        return "publishtokens";
    }
}
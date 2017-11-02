<?php
namespace WpGet\db;

class UsersTable extends TableBase
{
    public function  getFieldDefinition()
    { 
        return array(
            'username' =>'string',
            'password' =>'string',
            'token'=>'string',
        );
    }

    
    public function getTableName()
    {
        return "user";
    }
}
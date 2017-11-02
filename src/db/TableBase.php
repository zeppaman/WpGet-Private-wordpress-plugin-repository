<?php

namespace WpGet\db;

abstract class TableBase
{
    public abstract function getFieldDefinition();
    public abstract function getTableName();

    function setExtraConfig()
    {
        //Do nothing by default, override when needed
    }

    //field list
    public function getBaseFields()
    {
        return array(
            'id' =>'bigIncrements',
            'created_at'=>'timestamp',
            'updated_at'=>'timestamp',
        );
    }

    function getAllColumns()
    {
        return array_merge($this->getBaseFields(),$this->getFieldDefinition());
    }

}
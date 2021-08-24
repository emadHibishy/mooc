<?php

class userGroupModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $group_name;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'users_groups';
    public static $primaryKey = 'group_id';
    public static $tableSchema = array(
        'group_name' => self::DATA_TYPE_STR 
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addUserGroup($data)
    {
        return $this->create($data);
    }

    public function updateUserGroup($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteUserGroup($id)
    {
        return $this->delete($id);
    }
    
    public function getUserGroups()
    {
        return $this->read();
    }
    public function getUserGroup($field)
    {
        return $this->readByParams($field);
    }
    public function getUserGroupById($id)
    {
        return $this->readByParams( [static::$primaryKey => $id]);
    }
    
}
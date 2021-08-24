<?php

class coursesCategoriesModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $category_name;
    public $created_by;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'courses_categories';
    public static $primaryKey = 'category_id';
    public static $tableSchema = array(
        'category_name' => self::DATA_TYPE_STR ,
        'created_by'          => self::DATA_TYPE_INT
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addCategory($data)
    {
        return $this->create($data);
    }

    public function updateCategory($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->delete($id);
    }
    
    public function getCategories()
    {
        $sql = 'SELECT `cat`.* , `users`.`username` FROM `courses_categories` `cat` LEFT OUTER JOIN `users` ON `users`.`user_id` = `cat`.`created_by`';
        return $this->read('', [], $sql);
    }

    public function getCategory($field)
    {
        return $this->readByParams($field);
    }

    public function getCategoryById($id)
    {
        return $this->readByParams([static::$primaryKey => $id]);
    }
    
}
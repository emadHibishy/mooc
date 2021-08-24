<?php


class usersModel extends ModelHandler
{

    private $userData;

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $username;
    public $email;
    public $password;
    public $image;
    public $group_id;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'users';
    public static $primaryKey = 'user_id';
    public static $tableSchema = array(
        'username' => self::DATA_TYPE_STR ,
        'email'          => self::DATA_TYPE_STR,
        'password'  => self::DATA_TYPE_STR,
        'image'         => self::DATA_TYPE_STR,
        'group_id'   => self::DATA_TYPE_INT
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addUser($data)
    {
        return $this->create($data);
    }

    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteUser($id)
    {
        return $this->delete($id);
    }
    
    public function getUsers()
    {
        return $this->read('', [], 'SELECT `users`.* , `ug`.`group_name` FROM `users`  LEFT OUTER JOIN `users_groups` `ug` ON `users`.`group_id` = `ug`.`group_id`');
    }

    public function getUsersByGroup($groupId)
    {
        return $this->read(' WHERE group_id = :group_id', 'group_id',$groupId);
    }

    public function searchUsers($keyword)
    {
        $stmt = 'SELECT * FROM `users` WHERE `username` LIKE  :keyword or `email` LIKE :keyword';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':keyword', "%$keyword%", self::DATA_TYPE_STR);
        if($stmt->execute())
            $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        else
            $this->setError($stmt->errorInfo()[2]);
        return isset($result) && is_array($result) && !empty($result) ? $result : null;
    }

    public function login($username, $password)
    {
        $user = $this->readByParams(['username' => $username, 'password' => $password]);
        if(is_array($user) && !empty($user)){
            $this->userData = $user;
            return true;
        }elseif($user === null){
            $this->setError('Invalid Username or password.');
        }
        return false;
    }

    public function getUserById($id)
    {
        return $this->readByParams([static::$primaryKey => $id]);
    }

    public function getUserData()
    {
        return $this->userData;
    }
    
}
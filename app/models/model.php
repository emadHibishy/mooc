<?php


class ModelHandler
{
    private $errors = array();

    /* 
    =================
    =   Constants   =
    =================
    */
    const DATA_TYPE_BOOL = PDO::PARAM_BOOL;
    const DATA_TYPE_STR = PDO::PARAM_STR;
    const DATA_TYPE_INT = PDO::PARAM_INT;
    const DaTA_TYPE_DATE = 7;
    const DATA_TYPE_DECIMAL = 4;
    const DATA_TYPE_EMAIL = 6;
    protected static $connection;

    public function __construct()
    {
        if(isset(static::$connection)){
            return static::$connection;
        } else {
            static::$connection = new PDO('mysql://host='. DB_HOST.';dbname='. DB_NAME, DB_USER, DB_PASS, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
            ));
        }
    }
    /* 
    =====================
    =   Error Functions =
    =====================
    */    
    public function setError($error)
    {
        if(is_array($error))
            $this->errors = $error;
        else
            $this->errors[] = $error;
    }

    public function getError()
    {
        return $this->errors;
    }
    /* 
    =========================
    =   Helper Functions    =
    =========================
    */
    private function setTableCols($data)
    {
        foreach($data as $col => $val)
        {
            $this->$col = $val;
        }
    }

    private function buildParams()
    {
        $bindParams = '';
        foreach(static::$tableSchema as $col => $type){
            $bindParams .= "$col = :$col ," ;
        }
        return $bindParams;
    }

    private function prepareParams(PDOStatement $stmt)
    {
        foreach(static::$tableSchema as $col => $type){
            if ($type == 4) {
                $filterdVal = filter_var($this->$col, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(":{$col}" ,$filterdVal);
            } elseif ($type == 6) {
                $filterdVal = filter_var($this->$col, FILTER_SANITIZE_EMAIL);
                $stmt->bindValue(":{$col}" ,$filterdVal);
            } else {
                $stmt->bindValue(":{$col}", $this->$col, $type);
            }
        }
    }
    /* 
    =====================
    =   Crud Functions  =
    =====================
    */
    protected function create($data)
    {
        $this->setTableCols($data);
        $stmt = 'INSERT INTO '. static::$tableName . ' SET '. trim($this->buildParams(), ',');
        $stmt = static::$connection->prepare($stmt);
        $this->prepareParams($stmt);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    protected function update($id, $data)
    {
        $this->setTableCols($data);
        $stmt = 'UPDATE '. static::$tableName . ' SET '. trim($this->buildParams(), ',') . ' WHERE '. static::$primaryKey .' = :'. static::$primaryKey;
        $stmt = static::$connection->prepare($stmt);
        $this->prepareParams($stmt);
        $stmt->bindValue(':'. static::$primaryKey, $id, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    protected function read($extra = '',  $fields = [], $sql = '')
    {
        $sql = empty($sql) ? 'SELECT * FROM '.static::$tableName : $sql;
        $stmt = $sql .  $extra;
        $stmt = static::$connection->prepare($stmt);
        if(!empty($extra) && !empty($fields)){
            foreach($fields as $key => $value){
                $stmt->bindValue(':'. $key , $value);
            }
        }
        if ($stmt->execute()) 
            $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
         else
            $this->setError($stmt->errorInfo()[2]);
        return isset($result) && is_array($result) && !empty($result) ? $result : null;
    }

    protected function readByParams($fields, $extra = '')
    {
        if($extra == ''){
            $extra = ' WHERE ';
            foreach($fields as $key => $value){
                $extra .= "$key = :$key and ";
            }
            $extra = substr($extra,0,-4).'LIMIT 1';
        }
        $result = $this->read($extra, $fields);
        return is_array($result) ? $result[0] : $result;
    }

    protected function delete($id)
    {
        $stmt = 'DELETE FROM '. static::$tableName .' WHERE '. static::$primaryKey .' = :' . static::$primaryKey;
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':' . static::$primaryKey, $id, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

}
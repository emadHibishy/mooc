<?php

class lessonCommentsModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $comment_content;
    public $comment_parent;
    public $comment_user;
    public $comment_lesson;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'lesson_comments';
    public static $primaryKey = 'comment_id';
    public static $tableSchema = array(
        'comment_content' => self::DATA_TYPE_STR ,
        'comment_parent'   => self::DATA_TYPE_INT,
        'comment_user'        => self::DATA_TYPE_INT,
        'comment_lesson'    => self::DATA_TYPE_INT
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addLessonComment($data)
    {
        return $this->create($data);
    }

    public function updateLessonComment($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteLessonComment($id)
    {
        return $this->delete($id);
    }
    
    public function getLessonComments($lessonId)
    {
        return $this->read(' WHERE comment_lesson = :comment_lesson', ['comment_lesson' => $lessonId]);
    }

    public function getUserComments($userId)
    {
        return $this->read(' WHERE comment_user = :comment_user', ['comment_user' => $userId]);
    }

    public function getLessonCommentById($id)
    {
        return $this->readByParams( [static::$primaryKey => $id]);
    }
    
}
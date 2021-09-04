<?php

class courseReviewsModel extends ModelHandler
{

    /*
    =================================
    =   Updatable Schema Fields     =
    =================================
    */
    public $review_content;
    public $user_id;
    public $course_id;

    /*
    ==========================
    =   Static Variables     =
    ==========================
    */
    public static $tableName = 'course_reviews';
    public static $primaryKey = 'review_id';
    public static $tableSchema = array(
        'review_content' => self::DATA_TYPE_STR ,
        'user_id'        => self::DATA_TYPE_INT,
        'course_id'    => self::DATA_TYPE_INT
    );

    /*
    =======================
    =   Crud Operations   =
    =======================
    */
    public function addCourseReview($data)
    {
        return $this->create($data);
    }

    public function updateCourseReview($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteCourseReview($id)
    {
        return $this->delete($id);
    }
    
    public function getCourseReviews($courseId)
    {
        $sql = "SELECT `r`.`review_id`, `r`.`review_content`, `u`.`username`, `u`.`image` FROM `course_reviews` `r` LEFT OUTER JOIN `users` `u` ON `r`.`user_id` = `u`.`user_id`";
        return $this->read(" WHERE `r`.`course_id` = :course_id", ['course_id' => $courseId], $sql);
    }

    public function getUserReviewss($userId)
    {
        return $this->read(' WHERE user_id = :user_id', ['user_id' => $userId]);
    }

    public function getCourseReviewById($id)
    {
        return $this->readByParams( [static::$primaryKey => $id]);
    }
    
}
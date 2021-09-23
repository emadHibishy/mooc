<?php


class coursesModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $course_title;
    public $course_description;
    public $course_cover;
    public $course_category;
    public $course_instructor;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'courses';
    public static $primaryKey = 'course_id';
    public static $tableSchema = array(
        'course_title'                  => self::DATA_TYPE_STR ,
        'course_description'    => self::DATA_TYPE_STR,
        'course_price'          => self::DATA_TYPE_STR,
        'course_cover'          => self::DATA_TYPE_STR,
        'course_category'       => self::DATA_TYPE_INT,
        'course_instructor'     => self::DATA_TYPE_INT,
        'updated_at'            => self::DaTA_TYPE_DATE
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addCourse($data)
    {
        return $this->create($data);
    }

    public function updateCourse($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteCourse($id)
    {
        return $this->delete($id);
    }

    private function getCourseData($extra = '', $fields=[])
    {
        $sql = 'SELECT `c`.*, `cc`.`category_name`, `users`.`username` FROM `courses` `c` LEFT OUTER JOIN `courses_categories` `cc` ON `c`.`course_category` = `cc`.`category_id` LEFT OUTER JOIN `users` ON `c`.`course_instructor` = `users`.`user_id`';
        return $this->read($extra, $fields, $sql);
    }

    public function getLatestCourses()
    {
        return $this->getCourseData(" ORDER BY `course_id` DESC LIMIT 10");
    }
    
    public function getCourses()
    {
        return $this->getCourseData();
    }

    public function getCoursesByCategory($courseCategory)
    {
        return $this->getCourseData(' WHERE course_category = :course_category', ['course_category' => $courseCategory]);
    }

    public function getCoursesByInstructor($Instructor)
    {
        return $this->getCourseData(' WHERE course_instructor = :course_instructor', ['course_instructor' => $Instructor]);
    }

    public function getCoursesByCategoryAndInstructor($categoryId, $InstructorId)
    {
        return $this->getCourseData(' WHERE course_category = :course_category and course_instructor = :course_instructor', ['course_category' => $categoryId, 'course_instructor' => $InstructorId]);
    }

    // public function searchCourses($keyword)
    // {
    //     $stmt = 'SELECT * FROM `courses` WHERE `course_title` LIKE  :keyword or `course_description` LIKE :keyword';
    //     $stmt = static::$connection->prepare($stmt);
    //     $stmt->bindValue(':keyword', "%$keyword%", self::DATA_TYPE_STR);
    //     if($stmt->execute())
    //         $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     else
    //         $this->setError($stmt->errorInfo()[2]);
    //     return isset($result) && is_array($result) && !empty($result) ? $result : null;
    // }


    public function getCourseById($id)
    {
        return $this->readByParams( [static::$primaryKey => $id]);
    }
}
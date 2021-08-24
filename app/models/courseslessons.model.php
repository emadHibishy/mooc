<?php

class coursesLessonsModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $lesson_title;
    public $lesson_description;
    public $lesson_cover;
    public $lesson_video;
    public $lesson_duration;
    public $lesson_section;
    public $lesson_course;


    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'courses_lessons';
    public static $primaryKey = 'lesson_id';
    public static $tableSchema = array(
        'lesson_title'                  => self::DATA_TYPE_STR ,
        'lesson_description'    => self::DATA_TYPE_STR ,
        'lesson_cover'               => self::DATA_TYPE_STR ,
        'lesson_video'               => self::DATA_TYPE_STR ,
        'lesson_duration'         => self::DATA_TYPE_INT ,
        'lesson_section'            => self::DATA_TYPE_INT,
        'lesson_course'             => self::DATA_TYPE_INT ,
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addCourseLesson($data)
    {
        return $this->create($data);
    }

    public function updateCourseLesson($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteCourseLesson($id)
    {
        return $this->delete($id);
    }
    
    // public function getCourseLessons($courseId)
    // {
    //     return $this->readByParams(' WHERE lesson_course = :lesson_course ORDER BY lesson_id', ['lesson_course' => $courseId]);
    // }

    public function getCountCourseLessons($courseId)
    {
        $stmt = 'SELECT COUNT(`lesson_id`) as `lessons` FROM `courses_lessons`';
        $lessons = $this->read(' WHERE `lesson_course` = :lesson_course GROUP BY `lesson_course`', ['lesson_course' => $courseId], $stmt);
        if (is_null($lessons) || empty($lessons)) {
            return 'No lessons Added To This Course Yet';
        } else {
            return $lessons[0]['lessons'];
        }
    }

    public function getSectionLessons($sectionId)
    {
        return $this->read(' WHERE lesson_section = :lesson_section', ['lesson_section' => $sectionId]);
    }

    // public function getLessons ($courseId, $instructorId)
    // {
    //     $stmt = 'SELECT cl.*, co.course_title, u.username 
    //     FROM courses_lessons cl 
    //     LEFT OUTER JOIN courses co ON cl.lesson_course = co.course_id 
    //     LEFT OUTER JOIN users u ON cl.lesson_instructor = u.user_id 
    //     WHERE cl.lesson_course = :lesson_course && cl.lesson_instructor = :lesson_instructor';
    //     $stmt = static::$connection->prepare($stmt);
    //     $stmt->bindValue(':lesson_course', $courseId, self::DATA_TYPE_INT);
    //     $stmt->bindValue(':lesson_instructor', $instructorId, self::DATA_TYPE_INT);
    //     if($stmt->execute())
    //         $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     else
    //         $this->setError($stmt->errorInfo()[2]);
    //     return isset($result) && is_array($result) && !empty($result) ? $result : null;
    // }

    public function getLessonById($id)
    {
        return $this->readByParams( static::$primaryKey, $id);
    }
    
}
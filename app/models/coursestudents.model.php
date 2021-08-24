<?php

class courseStudentsModel extends ModelHandler
{

    /*
    =================================
    =   Updatable Schema Fields     =
    =================================
    */
    public $course_id;
    public $student_id;
    public $student_rate;
    public $is_approved;

    /*
    ==========================
    =   Static Variables     =
    ==========================
    */
    public static $tableName = 'course_students';
    public static $primaryKey = 'group_id';
    public static $tableSchema = array(
        'course_id'         => self::DATA_TYPE_INT,
        'student_id'       => self::DATA_TYPE_INT,
        'student_rate'  => self::DATA_TYPE_DECIMAL,
        'is_approved'   => self::DATA_TYPE_BOOL
    );

    /* 
    =====================
    =   course info     =
    =====================
    */
    public function getCourseCountStudent($courseId)
    {
        $stmt = 'SELECT COUNT(`student_id`) AS `students` FROM `course_students`';
        $studentsCount = $this->read(' WHERE `course_id` = :course_id GROUP BY `course_id`', ['course_id' => $courseId], $stmt);
        if (is_null($studentsCount) || empty($studentsCount)) {
           return 'No Students Registered For This Course Yet';
        } else {
            return $studentsCount[0]['students'];
        }
    }

    public function getCourseRate($courseId)
    {
        $stmt = 'SELECT SUM(`student_rate`) AS `sum_rate`, COUNT(`student_id`) AS `students_count` FROM `course_students`';
        $data = $this->read(" WHERE `student_rate` > '0' && `course_id` = :course_id", ['course_id'=>$courseId], $stmt);
        if (is_null($data) || empty($data) || $data[0]['students_count'] == 0) {
            return 'No Rates Given For This Course Yet';
        } else {
            $rate = $data[0]['sum_rate'] / $data[0]['students_count'];
            return $rate;
        }
    }

    /*
    =======================
    =   Crud Operations   =
    =======================
    */

    public function addStudentToCourse($data)
    {
        return $this->create($data);
    }

    public function deleteStudentFromCourse($courseId, $studentId)
    {
        $stmt = 'DELETE FROM '. static::$tableName.' WHERE course_id = :course_id && student_id = :student_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':course_id', $courseId, self::DATA_TYPE_INT);
        $stmt->bindValue(':student_id', $studentId, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    public function updateStudentRate($courseId, $studentId, $rate)
    {
        $stmt = 'Update '. static::$tableName.' SET student_rate = :student_rate WHERE course_id = :course_id && student_id = :student_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':student_rate', $rate, self::DATA_TYPE_INT);
        $stmt->bindValue(':course_id', $courseId, self::DATA_TYPE_INT);
        $stmt->bindValue(':student_id', $studentId, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    public function isStudentJoinedCourse($courseId, $studentId)
    {
        $stmt = 'SELECT * FROM '. static::$tableName .' WHERE course_id = :course_id && student_id = :student_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':course_id', $courseId, self::DATA_TYPE_INT);
        $stmt->bindValue(':student_id', $studentId, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    public function confirmStudentSubscription($courseId, $studentId)
    {
        $stmt = 'UPDATE '. static::$tableName.' SET is_approved = :is_approved WHERE course_id = :course_id && student_id = :student_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':is_approved', true, self::DATA_TYPE_INT);
        $stmt->bindValue(':course_id', $courseId, self::DATA_TYPE_INT);
        $stmt->bindValue(':student_id', $studentId, self::DATA_TYPE_INT);
        if($stmt->execute())
            return true;
        $this->setError($stmt->errorInfo()[2]);
        return false;
    }

    public function getCourseStudents($courseId)
    {
        $stmt = 'SELECT `u`.`user_id`, `u`.`username`, `u`.`email`, `u`.`image`, `cs`.`is_approved` FROM `users` `u` LEFT OUTER JOIN `course_students` `cs` ON `cs`.`student_id` = `u`.`user_id` WHERE `cs`.`course_id` = :course_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':course_id', $courseId, self::DATA_TYPE_INT);
        if($stmt->execute())
            $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        else
            $this->setError($stmt->errorInfo()[2]);
        return isset($result) && is_array($result) && !empty($result) ? $result : null;
    }

    public function getStudentCourses($studentId)
    {
        $stmt = 'SELECT `c`.* FROM `courses` `c` LEFT OUTER JOIN `course_students` `cs` ON `cs`.`course_id` = `c`.`course_id` WHERE `cs`.`student_id` = :student_id';
        $stmt = static::$connection->prepare($stmt);
        $stmt->bindValue(':student_id', $studentId, self::DATA_TYPE_INT);
        if($stmt->execute())
            $result  = $stmt->fetchAll(PDO::FETCH_ASSOC);
        else
            $this->setError($stmt->errorInfo()[2]);
        return isset($result) && is_array($result) && !empty($result) ? $result : null;
    }
    
}
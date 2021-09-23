<?php

class courseSectionsModel extends ModelHandler
{

    /*
    ========================
    =   Updatable Schema Fields     =
    ========================
    */
    public $section_title;
    public $section_course;

    /*
    =================
    =   Static Variables     =
    =================
    */
    public static $tableName = 'courses_sections';
    public static $primaryKey = 'section_id';
    public static $tableSchema = array(
        'section_title'         => self::DATA_TYPE_STR ,
        'section_course'    => self::DATA_TYPE_INT
    );

    /*
    =================
    =   Crud Operations   =
    =================
    */
    public function addCourseSection($data)
    {
        return $this->create($data);
    }

    public function updateCourseSection($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteCourseSection($id)
    {
        return $this->delete($id);
    }
    
    public function getCourseSections($courseId)
    {
        return $this->read(' WHERE section_course = :section_course ORDER BY section_id', ['section_course' => $courseId], 'SELECT `se`.*, `c`.`course_title` FROM `courses_sections` `se` LEFT OUTER JOIN `courses` `c` ON `se`.`section_course` = `c`.`course_id`');
    }

    // public function getCountCourseSections($courseId)
    // {
    //     $stmt = 'SELECT COUNT(`section_id`) as `sections` FROM `courses_sections`';
    //     $sections = $this->read(' WHERE `section_course` = :section_course GROUP BY `section_course`', ['section_course' => $courseId], $stmt);
    //     if (is_null($sections) || empty($sections)) {
    //         return 'No Sections Added To This Course Yet';
    //     } else {
    //         return $sections[0]['sections'];
    //     }
    // }

    public function getCourseSectionById($id)
    {
        return $this->readByParams( [static::$primaryKey => $id]);
    }
    
}
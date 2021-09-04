<?php

class Factory
{


    public static function generateUsersGroupDataArray($groupName)
    {
        return array(
            'group_name' => $groupName
        );
    }

    public static function generateUserDataArray($username, $email, $password, $imageUrl, $groupId)
    {
        return array(
            'username' => $username,
            'email'         => $email,
            'password' => $password,
            'image'        => $imageUrl,
            'group_id'  => $groupId
        );
    }

    public static function generateCourseDataArray($title, $description, $price, $cover, $category, $instructor)
    {
        return array(
            'course_title'      => $title,
            'course_description'=> $description,
            'course_price'      => $price,
            'course_cover'      => $cover,
            'course_category'   => $category,
            'course_instructor' => $instructor,
            'updated_at' => date('M jS Y', time())
        );
    }

    public static function generateCoursesCategoryDataArray($name, $createdBy)
    {
        return array(
            'category_name' => $name,
            'created_by'         => $createdBy
        );
    }

    public static function generateCourseSectionsDataArray($title, $courseId)
    {
        return array(
            'section_title'         => $title,
            'section_course'    => $courseId
        );
    }

    public static function generateCourseLessonDataArray($title, $description, $cover, $video, $duration, $section, $course)
    {
        return array(
            'lesson_title'                  => $title,
            'lesson_description'    => $description,
            'lesson_cover'               => $cover,
            'lesson_video'               => $video,
            'lesson_duration'         => $duration,
            'lesson_section'            => $section,
            'lesson_course'             => $course,
        );
    }

    public static function generateLessonCommentDataArray($content, $parent, $user, $lesson)
    {
        return array(
            'comment_content' => $content,
            'comment_parent'   => $parent,
            'comment_user'        => $user,
            'comment_lesson'    => $lesson,
        );
    }

    public static function generateCourseReviewsDataArray($content, $course, $user)
    {
        return array(
            'review_content' => $content,
            'course_id'    => $course,
            'user_id'        => $user,
        );
    }

    public static function generateCourseStudentsDataArray($course, $student, $rate, $isApproved)
    {
        return array(
            'course_id'         => $course,
            'student_id'       => $student,
            'student_rate'   => $rate,
            'is_approved'    => $isApproved
        );
    }
}
<?php

function capitalizeCourseTitle($courses)
{
    if(count($courses) > 0)
    {
        $newCourses = array();
        foreach ($courses as $course )
        {
            $course['course_title'] = strtoupper($course['course_title']);
            $newCourses[] = $course; 
        }
    }
    return $newCourses;
}

add_filter('display_courses', 'capitalizeCourseTitle');
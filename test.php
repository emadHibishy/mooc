<?php
// require_once 'globals.php';
// require_once MODELS . '/coursesections.model.php';
// require_once MODELS . '/courseslessons.model.php';

// $sectionModel = new courseSectionsModel();
// $lessonModel = new coursesLessonsModel();

// $sections = $sectionModel->getCourseSections(3);
// $course = [];
// foreach ($sections as $section) {
//     $lessons = $lessonModel->getSectionLessons($section['section_id']);
//     $section = array_merge($section, ['lessons' => $lessons]);
//     array_push($course, $section);
// }

echo '<pre>';
var_dump($_SERVER['HTTP_REFERER']);
<?php

class instructorController extends Controller
{
    private $courseModel;
    private $userGroupModel;
    private $usersModel;
    private $catsModel;
    private $lessonModel;
    public function __construct()
    {
        $this->checkPermission();
        $this->catsModel = new coursesCategoriesModel();
        $this->courseModel = new coursesModel();
        $this->userGroupModel = new userGroupModel();
        $this->usersModel = new usersModel();
        $this->lessonModel = new coursesLessonsModel();
        $this->_view();
    }
    // Helper Functions
    /* 
    =====================
    =   view function   =
    =====================
    * check the url
    * predict view
    * call the function
    */
    private function _view()
    {
        $url = explode('/', trim($_SERVER['REQUEST_URI']));
        $view = end($url);
        if($view === ''){
            $view = 'index.php';
        }elseif(!empty($_GET)) {
            $view = explode('?', $view)[0];
        }
        $view = str_replace('.php', '', $view);
        $this->$view($view);
    }
    private function index($view)
    {
        $this->render($view);
    }

    /*===========================
    =   is instructor Course    =
    =============================
    * takes the course id and check if the course belongs to the instructor
    */
    private function isInstructorCourse($courseId)
    {
        $course = $this->courseModel->getCourseById($courseId);
        $instructorCourses = $this->courseModel->getCoursesByInstructor($_SESSION['user']['user_id']);
        $isInsCourse = false;
        if( is_array($instructorCourses) && !empty($instructorCourses) && is_array($course) && !empty($course) ) {
            foreach($instructorCourses as $insCourse) {
                if($course['course_id'] == $insCourse['course_id'])
                    $isInsCourse = true;
            }
        }
        return $isInsCourse;
    }



    /* 
    ========================
    =   courses functions  =
    ========================
    * return instructor courses
    */
    private function courses($view)
    {
        $courses = $this->courseModel->getCoursesByInstructor($_SESSION['user']['user_id']);
        if(empty($courses)){
            $this->setError('You Didn\' Add Courses yet.');
            $this->render($view);
        }else 
            $this->render($view, $courses);
    }

    /* 
    ====================
    =   delete course  =
    ====================
    * instructor can delete a course
    */
    private function deletecourse($view) {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course'];
            if($this->courseModel->deleteCourse($courseId))
                $this->setSuccess('Course Deleted Successfully');
            else
                $this->setError('Something went Wrong, Course didn\'t deleted');
            Redirect::redirect('courses.php');
        } else
            Redirect::redirect('index.php');
    }

    /* 
    =================
    =   add course  =
    =================
    * instructor can add a course
    */
    private function addcourse($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $courseName = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $coursePrice= filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $categoryId = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
            $imgsTypes = array('jpg', 'jpeg', 'png');
            $imgType = explode('/',$_FILES['cover']['type'][0]);
            $extnsn = end($imgType);
            if(in_array($extnsn, $imgsTypes)){
                $imgName = time(). rand(1,10000).$_FILES['cover']['name'][0];
                if(move_uploaded_file($_FILES['cover']['tmp_name'][0], '../uploads/'.$imgName)){
                    // echo $categoryId;
                    if($this->courseModel->addCourse(Factory::generateCourseDataArray($courseName, $description, $coursePrice,'uploads/'.$imgName, $categoryId, $_SESSION['user']['user_id']))){
                        Redirect::redirect('courses.php');
                    }else
                        $this->setError($this->courseModel->getError());
                }
            }else{
                $this->setError('Image must be of type jpg, jpeg or png');
            }
        }
        $cats = $this->catsModel->getCategories();
        $this->render($view, $cats);
    }


    /* 
    =====================
    =   update course   =
    =====================
    * instructor can update course
    */
    private function updatecourse($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $courseId   = filter_var($_POST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $courseName = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $coursePrice= filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $categoryId = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);
            if(isset($_POST['cover'])){
                $imgsTypes = array('jpg', 'jpeg', 'png');
                $imgType = explode('/',$_FILES['cover']['type'][0]);
                $extnsn = end($imgType);
                if(in_array($extnsn, $imgsTypes)){
                    $imgName = time(). rand(1,10000).$_FILES['cover']['name'][0];
                    move_uploaded_file($_FILES['cover']['tmp_name'][0], '../uploads/'.$imgName);
                }else{
                    $this->setError('Image must be of type jpg, jpeg or png');
                    return $this->render($view);
                }
            }else{
                $course = $this->courseModel->getCourseById($courseId);
                $imgName = substr($course['course_cover'], 7);
            }
            if($this->courseModel->updateCourse($courseId, Factory::generateCourseDataArray($courseName, $description, $coursePrice,'uploads/'.$imgName, $categoryId, $_SESSION['user']['user_id']))){
                Redirect::redirect('courses.php');
            }else
                $this->setError($this->courseModel->getError());
        }elseif(isset($_GET['id'])) {
            $id = is_numeric($_GET['id']) ? $_GET['id'] : 0;
            $course = $this->courseModel->getCourseById($id);
            $categories = $this->catsModel->getCategories();
            if($course['course_instructor'] == $_SESSION['user']['user_id']) {
                if(!empty($course))
                    return $this->render($view, [$course, $categories]);
                else 
                    $this->setError('Course Not Found');
            } else{
                $this->setError('You are not allowed to see this content');
                return Redirect::redirect("course.php?id={$course['course_id']}");
            }
        }else
            $this->setError('Course Not Found');
        $this->render($view);
    }

    /*===============================
    =   approve student to course   =
    =================================
    * instructor can approve students registered th course
    */
    private function approvestudent($view)
    {
        $studentId = isset($_GET['studentid']) ? (int)$_GET['studentid'] : 0;
        $courseId = isset($_GET['courseid']) ? (int)$_GET['courseid'] : 0;
        $course = $this->courseModel->getCourseById($courseId);
        if(is_array($course) && !empty($course)) {
            if ($course['course_instructor'] == $_SESSION['user']['user_id'] ) {
                $courseStudentsModel = new courseStudentsModel();
                if($courseStudentsModel->isStudentJoinedCourse($courseId, $studentId)){
                    if($courseStudentsModel->confirmStudentSubscription($courseId, $studentId))
                        $this->setSuccess('Student Approved Succeessfully');
                    else
                        $this->setError($courseStudentsModel->getError());
                } else 
                    $this->setError('student not registerd in this course');
            } else
                $this->setError('Sorry, You haven\'t the authority to see this content');
            Redirect::redirect("course.php?id={$courseId}");
        } else
            $this->setError('Course Not Found');
        Redirect::redirect('index.php');
    }

    /* 
    =============================
    =   single course function  =
    =============================
    * coursse page with its sections, lessons, students registered
    */
    private function course($view)
    {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $courseId = filter_var($_GET['id'] , FILTER_SANITIZE_NUMBER_INT);
            $course = $this->courseModel->getCourseById($courseId);
            $data = [];
            $courseStudentsModel = new courseStudentsModel();
            $courseSectionsModel = new courseSectionsModel();
            $courseReviewsModel  = new courseReviewsModel();
            if (is_array($course) && !empty($course)) {
                if($course['course_instructor'] == $_SESSION['user']['user_id']) {
                    // course numbers 
                    $students = $courseStudentsModel->getCourseStudents($courseId);
                    $courseStudentsNo = is_array($students) && !empty($students) ? count($students) : 0;
                    $courseRate = $courseStudentsModel->getCourseRate($courseId);
                    $courseSectionsNo = $courseSectionsModel->getCountCourseSections($courseId);
                    $courseLessonsNo = $this->lessonModel->getCountCourseLessons($courseId);
                    $courseReviews = $courseReviewsModel->getCourseReviews($courseId);
                    $course = array_merge($course, ['students_no' => $courseStudentsNo]);
                    $course = array_merge($course, ['sections_no' => $courseSectionsNo]);
                    $course = array_merge($course, ['lessons_no' => $courseLessonsNo]);
                    $course = array_merge($course, ['rate' => $courseRate]);
                    $data = array_merge($data, ['course_details' => $course]);
                    $data = array_merge($data, ['reviews' => $courseReviews]);

                    $course_sections_lessons = [];
                    // course actual data
                    $courseSections = $courseSectionsModel->getCourseSections($courseId);
                    if (is_array($courseSections) && !empty($courseSections)) {
                        foreach ($courseSections as $section) {
                            $courseLessons = $this->lessonModel->getSectionLessons($section['section_id']);
                            if (is_array($courseLessons) && !empty($courseLessons)) {
                                $section = array_merge($section, ['lessons' => $courseLessons]);
                            } else
                                $section = array_merge($section, ['lessons' => 'No Lessons Added to This Section Yet']);
                            array_push($course_sections_lessons, $section);
                        }
                    } else 
                        array_push($course_sections_lessons, 'No Sections Added to this course yet');
                    $data = array_merge($data, ['course' => $course_sections_lessons]);
                    if (is_array($students) && !empty($students))
                        $data = array_merge($data, ['students' => $students]);
                    else
                        $data = array_merge($data, ['students' => 'No students Registered in this Course yet']);
                    return $this->render($view, $data);
                } else {
                    $this->setError('Not Permitted to see this content');
                    return Redirect::redirect('courses.php');
                }
            } else
                $this->setError($this->courseModel->getError());
        } else 
            $this->setError('Course Not Found');
        $this->render($view);
    }

    /* 
    =========================
    =   add course section  =
    =========================
    * instructor can add section to course
    */
    private function addcoursesection($view)
    {
        $sectionModel = new courseSectionsModel();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['course_id'], FILTER_SANITIZE_NUMBER_INT);
            $section = filter_var($_POST['section_name'], FILTER_SANITIZE_STRING);
            if( $sectionModel->addCourseSection(Factory::generateCourseSectionsDataArray($section, $id))) {
                $this->setSuccess('Section Added Successfully');
                Redirect::redirect('course.php?id='.$id);
            } else {
                $this->setError('Section is not valid');
                Redirect::redirect("javascript:history.go(-1)");
            }
        } else {
            Redirect::redirect("javascript:history.go(-1)");
        }
    }

    /* 
    =========================
    =   add section lesson  =
    =========================
    * instructor can add lesson to section in the course
    */
    private function addsectionlesson($view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = filter_var($_POST['course'], FILTER_SANITIZE_NUMBER_INT);
            $sectionId = filter_var($_POST['section'], FILTER_SANITIZE_NUMBER_INT);
            $lessonUrl = filter_var($_POST['video'], FILTER_SANITIZE_URL);
            $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $imgsTypes = array('jpg', 'jpeg', 'png');
            $imgType = explode('/',$_FILES['img']['type']);
            $extnsn = strtolower(end($imgType));
            if(in_array($extnsn, $imgsTypes)){
                $imgName = time(). rand(1,10000).$_FILES['img']['name'];
                if(move_uploaded_file($_FILES['img']['tmp_name'], '../uploads/'.$imgName)){
                    if($this->lessonModel->addCourseLesson(Factory::generateCourseLessonDataArray($title, $description, $imgName, $lessonUrl, $duration, $sectionId, $courseId))){
                        $this->setSuccess('Lesson Added Successfully');
                        Redirect::redirect("course.php?id={$courseId}");
                    }else
                        $this->setError($this->courseModel->getError());
                }else{
                    $this->setError('unknown error while uploading image please try again later');
                }
            }else{
                $this->setError('Image must be of type jpg, jpeg or png');
            }
        } else {
            $sectionModel = new courseSectionsModel();
            if ( isset($_GET['id']) && isset($_GET['courseid']) ) {
                $secId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                $courseId = filter_var($_GET['courseid'], FILTER_SANITIZE_NUMBER_INT);
                $course = $this->courseModel->getCourseById($courseId);
                if($course['course_instructor'] == $_SESSION['user']['user_id']) {
                    if ( is_array($course) && !empty($course) ) {
                        $section = $sectionModel->getCourseSectionById($secId);
                        if (is_array($section) && !empty($section)) {
                            $courseSections = $sectionModel->getCourseSections($courseId);
                            $isCourseSection = false;
                            foreach ( $courseSections as $courseSection ) {
                                if ($courseSection['section_id'] === $section['section_id']) {
                                    $isCourseSection = true;
                                    break;
                                } 
                            }
                            if ($isCourseSection) {
                                $data = [
                                    'course' => $course,
                                    'sections' => $courseSections,
                                    'sectionId' => $secId
                                ];
                                return $this->render($view, $data);
                            } else 
                                $this->setError('This Section don\'t belong to this course');
                        } else 
                            $this->setError('Course Section Not Found');
                    } else 
                        $this->setError('Course Not Found');
                } else 
                    $this->setError('You have not the authority to see this content');
                return Redirect::redirect("course.php?id={$course['course_id']}");
            }
        }
        $this->render($view);
    }

    /* 
    ============================
    =   delete section lesson  =
    ============================
    * instructor can delete lesson in the course
    */
    private function deletesectionlesson($view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessonId = (int)$_POST['lesson'];
            $courseId = (int)$_POST['course'];
            if ($this->lessonModel->deleteCourseLesson($lessonId))
                $this->setSuccess('Lesson Deleted Successfully');
            else
                $this->setError('Something went wrong, Lesson not Deleted');
            Redirect::redirect("course.php?id={$courseId}");
        } else {
            Redirect::redirect('index.php');
        }
    }

    /* 
    ============================
    =   update section lesson  =
    ============================
    * instructor can update lesson in the course
    */
    private function updatelesson ($view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessonId = (int)$_POST['lesson_id'];
            $courseId = (int)$_POST['course'];
            $sectionId = (int)$_POST['section'];
            $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            $videoUrl = filter_var($_POST['video'], FILTER_SANITIZE_URL);
            $lesson = $this->lessonModel->getLessonById($lessonId); 
            $imgName = $lesson['lesson_cover'];
            if (isset($_FILES['img'])  && !empty($_FILES['img']['name'])) {
                $imgsTypes = array('jpg', 'jpeg', 'png');
                $imgType = explode('/',$_FILES['img']['type']);
                $extnsn = strtolower(end($imgType));
                if(in_array($extnsn, $imgsTypes)){
                    $imgName = time(). rand(1,10000).$_FILES['img']['name'];
                    if(move_uploaded_file($_FILES['img']['tmp_name'], '../uploads/'.$imgName)){
                        
                    }else{
                        $this->setError('unknown error while uploading image please try again later');
                        return Redirect::redirect($_SERVER['PHP_SELF']);
                    }
                }else{
                    $this->setError('Image must be of type jpg, jpeg or png');
                    return Redirect::redirect($_SERVER['PHP_SELF']);
                }
            }
            if($this->lessonModel->updateCourseLesson($lessonId, Factory::generateCourseLessonDataArray($title,$description, $imgName, $videoUrl, $duration, $sectionId, $courseId))) {
                $this->setSuccess('Lesson Updated Successfully');
                return Redirect::redirect("course.php?id={$courseId}");
            } else {
                $this->setError('something went wrong');
                return Redirect::redirect($_SERVER['PHP_SELF']);
            }
        } else {
            $lessonId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $lesson = $this->lessonModel->getLessonById($lessonId);
            if (is_array($lesson) && !empty($lesson)) {
                $sectionModel = new courseSectionsModel();
                $lessonSection = $sectionModel->getCourseSectionById($lesson['lesson_section']);
                $lesson_course = $this->courseModel->getCourseById($lessonSection['section_course']);
                if ($lesson_course['course_instructor'] == $_SESSION['user']['user_id']) {
                    return $this->render($view, ['lesson' => $lesson]);
                } else {
                    $this->setError('You are not allowed to see this lesson');
                    Redirect::redirect("course.php?id={$lesson_course['course_id']}"); 
                }
            } else {
                $this->setError('No Such Lesson In the database');
                Redirect::redirect("javascript:history.go(-1)");
            }
        }
    }
    
    /* 
    ===========================
    =   delete course review  =
    ===========================
    * instructor can delete course review written by students
    */
    private function deletereview($view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewId = (int)$_POST['review'];
            $courseId = (int)$_POST['course'];
            $reviewModel = new courseReviewsModel();
            if ($reviewModel->deleteCourseReview($reviewId)) 
                $this->setSuccess('Review Deleted Successfully');
            else 
                $this->setError('Something went wrong, try to delete later');
            Redirect::redirect("course.php?id={$courseId}");
        } else {
            Redirect::redirect('index.php');
        }
    }
}

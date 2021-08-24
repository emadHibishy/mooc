<?php

class instructorController extends Controller
{
    private $courseModel;
    private $userGroupModel;
    private $usersModel;
    private $catsModel;
    public function __construct()
    {
        $this->checkPermission();
        $this->catsModel = new coursesCategoriesModel();
        $this->courseModel = new coursesModel();
        $this->userGroupModel = new userGroupModel();
        $this->usersModel = new usersModel();
        $this->_view();
    }

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
                    if($this->courseModel->addCourse(Factory::generateCourseDataArray($courseName, $description, $coursePrice,'uploads/'.$imgName, 0,$categoryId, $_SESSION['user']['user_id']))){
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
            if($this->courseModel->updateCourse($courseId, Factory::generateCourseDataArray($courseName, $description, $coursePrice,'uploads/'.$imgName, 0,$categoryId, $_SESSION['user']['user_id']))){
                Redirect::redirect('courses.php');
            }else
                $this->setError($this->courseModel->getError());
        }elseif(isset($_GET['id'])) {
            $id = is_numeric($_GET['id']) ? $_GET['id'] : 0;
            $course = $this->courseModel->getCourseById($id);
            $categories = $this->catsModel->getCategories();
            if(!empty($course))
                return $this->render($view, [$course, $categories]);
            else 
                $this->setError('Course Not Found');
        }else{
            $this->setError('Course Not Found');
        }
        $this->render($view);
    }

    /* 
    =============================
    =   single course function  =
    =============================
    * coursse page with its sections, lessons, students registered
    */
    private function course($view)
    {
        $data = [];
        $courseStudentsModel = new courseStudentsModel();
        $courseSectionsModel = new courseSectionsModel();
        $courseLessonsModel = new coursesLessonsModel();
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $courseId = filter_var($_GET['id'] , FILTER_SANITIZE_NUMBER_INT);
            $course = $this->courseModel->getCourseById($courseId);
            if (is_array($course) && !empty($course)) {
                if($course['course_instructor'] == $_SESSION['user']['user_id']) {
                    // course numbers info
                    $courseStudentsNo = $courseStudentsModel->getCourseCountStudent($courseId);
                    $courseRate = $courseStudentsModel->getCourseRate($courseId);
                    $courseSectionsNo = $courseSectionsModel->getCountCourseSections($courseId);
                    $courseLessonsNo = $courseLessonsModel->getCountCourseLessons($courseId);
                    $course = array_merge($course, ['students_no' => $courseStudentsNo]);
                    $course = array_merge($course, ['sections_no' => $courseSectionsNo]);
                    $course = array_merge($course, ['lessons_no' => $courseLessonsNo]);
                    $course = array_merge($course, ['rate' => $courseRate]);
                    $data = array_merge($data, ['course_details' => $course]);

                    $course_sections_lessons = [];
                    // course actual data
                    $courseSections = $courseSectionsModel->getCourseSections($courseId);
                    if (is_array($courseSections) && !empty($courseSections)) {
                        foreach ($courseSections as $section) {
                            $courseLessons = $courseLessonsModel->getSectionLessons($section['section_id']);
                            if (is_array($courseLessons) && !empty($courseLessons)) {
                                $section = array_merge($section, ['lessons' => $courseLessons]);
                            } else
                                $section = array_merge($section, ['lessons' => 'No Lessons Added to This Section Yet']);
                            array_push($course_sections_lessons, $section);
                        }
                    } else 
                        array_push($course_sections_lessons, 'No Sections Added to this course yet');
                    $data = array_merge($data, ['course' => $course_sections_lessons]);
                    $students = $courseStudentsModel->getCourseStudents($courseId);
                    if (is_array($students) && !empty($students))
                        $data = array_merge($data, ['students' => $students]);
                    else
                        $data = array_merge($data, ['students' => 'No students Registered in this Course yet']);
                    return $this->render($view, $data);
                } else 
                    $this->setError('Not Permitted to see this content');
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
            $description= filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
            $imgsTypes = array('jpg', 'jpeg', 'png');
            echo $_POST['title'];
            echo '<pre>';
            print_r($_FILES['cover']);
            // $imgType = explode('/',$_FILES['cover']['type'][0]);
            // $extnsn = end($imgType);
            // if(in_array($extnsn, $imgsTypes)){
            //     $imgName = time(). rand(1,10000).$_FILES['cover']['name'][0];
            //     if(move_uploaded_file($_FILES['cover']['tmp_name'][0], '../uploads/'.$imgName)){
            //         if($this->courseModel->addCourse(Factory::generateCourseDataArray($courseName, $description, $coursePrice,'uploads/'.$imgName, 0,$categoryId, $_SESSION['user']['user_id']))){
            //             Redirect::redirect('courses.php');
            //         }else
            //             $this->setError($this->courseModel->getError());
            //     }
            // }else{
            //     $this->setError('Image must be of type jpg, jpeg or png');
            // }
        } else {
            $sectionModel = new courseSectionsModel();
            if ( isset($_GET['id']) && isset($_GET['courseid']) ) {
                $secId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
                $courseId = filter_var($_GET['courseid'], FILTER_SANITIZE_NUMBER_INT);
                $course = $this->courseModel->getCourseById($courseId);
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
                            $this->render($view, $data);
                        return $this->render($view, $data);
                        } else {
                            $this->setError('This Section don\'t belong to this course');
                        }
                    } else {
                        $this->setError('Course Section Not Found');
                    }
                } else {
                    $this->setError('Course Not Found');
                }
            }
            $this->render($view);
        }
    }
}
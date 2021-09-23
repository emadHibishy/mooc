<?php

class adminController extends Controller
{
    private $catModel;
    private $userGroupModel;
    private $usersModel;

    public function __construct()
    {
        $this->checkPermission();
        $this->catModel = new coursesCategoriesModel();
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

    /* 
    =================
    =   index page  =
    =================
    */
    private function index($view)
    {
        $this->render($view);
    }

    /* 
    =========================
    =   Category functions  =
    =========================
    * return all categories
    */
    private function categories($view)
    {
        $cats = $this->catModel->getCategories();
        if(empty($cats)){
            $this->setError('No Categories Added yet.');
            $this->render($view);
        }else 
            $this->render($view, $cats);
    }

    /* 
    ===================
    =   add Category  =
    ===================
    * admin can add category
    */
    private function addcategory($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $catName = filter_var($_POST['categoryName'], FILTER_SANITIZE_STRING);
            if(strlen($catName) < 4){
                $this->setError('Category Name must be more than 3 letters');
            } else {
                if($this->catModel->addCategory(Factory::generateCoursesCategoryDataArray($catName, $_SESSION['user']['user_id']))){
                    $this->setSuccess('Category Added Successfully');
                    return Redirect::redirect('categories.php');
                }
                else {
                    $this->setError($this->catModel->getError());
                }
            } 
        }
        $this->render($view);    
    }

    /* 
    ======================
    =   update Category  =
    ======================
    * admin can update category
    */
    private function updatecategory($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $catName = filter_var($_POST['categoryName'], FILTER_SANITIZE_STRING);
            $catId = filter_var($_POST['categoryId'], FILTER_SANITIZE_NUMBER_INT);
            if(strlen($catName) < 4){
                $this->setError('Category Name must be more than 3 letters');
            } else {
                if($this->catModel->updateCategory($catId,Factory::generateCoursesCategoryDataArray($catName, $_SESSION['user']['user_id']))){
                    $this->setSuccess('Category Updated Successfully');
                    return Redirect::redirect('categories.php');
                }
                else
                    $this->setError($this->catModel->getError());
            } 
        }elseif(isset($_GET['id'])) {
            $id = is_numeric($_GET['id']) ? $_GET['id'] : 0;
            $category = $this->catModel->getCategoryById($id);
            if(!empty($category))
                return $this->render($view, $category);
            else 
                $this->setError('Category Not Found');
        }else{
            $this->setError('Category Not Found');
        }
        $this->render($view);
    }

    /* 
    ======================
    =   delete Category  =
    ======================
    * admin can delete category
    */
    private function deletecategory($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryId = (int)$_POST['category'];
            $category = $this->catModel->getCategoryById($categoryId);
            if(is_array($category) && !empty($category)) {
                if($this->catModel->deleteCategory($categoryId)){
                    $this->setSuccess('Category Deleted Successfully');
                } else
                    $this->setError($this->catModel->getError());
            } else 
                $this->setError('Category Not Found');
        }
        Redirect::redirect('categories.php');
    }

    /* 
    =====================
    =   users function  =
    =====================
    * return all users
    */
    private function users($view)
    {
        $users = $this->usersModel->getUsers();
        $this->render($view, $users);
    }

    /* 
    ===============
    =   add user  =
    ===============
    * admin can add user
    */
    private function adduser($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $password = hashPassword($password);
            $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
            $groupId = filter_var($_POST['groupId'], FILTER_SANITIZE_NUMBER_INT);
            if(strlen($username) < 4){
                $this->setError('Username must be more than 3 letters');
                $this->render($view);
            }elseif(strlen(strlen($email) < 10)){
                $this->setError('email must be more than 10 letters');
                $this->render($view);
            }
            else {
                if($this->usersModel->addUser(Factory::generateUserDataArray($username, $email, $password, $image, $groupId))){
                    $this->setSuccess('User Added Successfully');
                    return Redirect::redirect('users.php');
                }
                else {
                    $this->setError($this->catModel->getError());
                    $this->render($view);
                }
            } 
        }else{
            $groups = $this->userGroupModel->getUserGroups();
            $this->render($view, $groups);
        }
    }

    /* 
    ==================
    =   update user  =
    ==================
    * admin can update user
    */
    private function updateuser($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $password = hashPassword($password);
            $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING);
            $groupId = filter_var($_POST['groupId'], FILTER_SANITIZE_NUMBER_INT);
            $userId = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
            if(strlen($username) < 4){
                $this->setError('Username must be more than 3 letters');
            } else {
                if($this->usersModel->updateUser($userId,Factory::generateUserDataArray($username, $email, $password, $image, $groupId))){
                    $this->setSuccess('User Updated Successfully');
                    return Redirect::redirect('users.php');
                }
                else
                    $this->setError($this->usersModel->getError());
            } 
        }elseif(isset($_GET['id'])) {
            $id = is_numeric($_GET['id']) ? $_GET['id'] : 0;
            $user = $this->usersModel->getUserById($id);
            if(!empty($user))
                return $this->render($view, $user);
            else 
                $this->setError('User Not Found');
        }else{
            $this->setError('User Not Found');
        }
        $this->render($view);
    }

    private function deleteuser($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $userId = (int)$_POST['user'];
            $user = $this->usersModel->getUserById($userId);
            if(is_array($user) && !empty($user)){
                if($this->usersModel->deleteUser($userId))
                    $this->setSuccess('User Deleted Successfully');
                else
                    $this->setError($this->usersModel->getError());
            }else
                $this->setError('User Not Found');
        }
        Redirect::redirect('users.php');
    }

    /* 
    =============================
    =   users groups functions  =
    =============================
    * if request = post => delete user group
    * if request = get => return all user group
    */
    private function usersgroups($view)
    {
        $usersGroups = $this->userGroupModel->getUserGroups();
        $this->render($view, $usersGroups);
    }

    /* 
    =====================
    =   add user group  =
    =====================
    * admin can add user group
    */
    private function addusergroup($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $group = filter_var($_POST['group'], FILTER_SANITIZE_STRING);
            if(strlen($group) < 4){
                $this->setError('Group Name must be more than 3 letters');
            } else {
                if($this->userGroupModel->addUserGroup(Factory::generateUsersGroupDataArray($group))){
                    $this->setSuccess('Group Added Successfully');
                    return Redirect::redirect('usersgroups.php');
                }
                else {
                    $this->setError($this->userGroupModel->getError());
                }
            } 
        }
        $this->render($view);  
    }

    /* 
    ========================
    =   update user group  =
    ========================
    * admin can update user group
    */
    private function updateusergroup($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $group = filter_var($_POST['groupName'], FILTER_SANITIZE_STRING);
            $groupId = filter_var($_POST['groupId'], FILTER_SANITIZE_NUMBER_INT);
            if(strlen($group) < 4){
                $this->setError('Group Name must be more than 3 letters');
            } else {
                if($this->userGroupModel->updateUserGroup($groupId,Factory::generateUsersGroupDataArray($group))){
                    $this->setSuccess('Group Updated Successfully');
                    return Redirect::redirect('usersgroups.php');
                }
                else
                    $this->setError($this->userGroupModel->getError());
            } 
        }elseif(isset($_GET['id'])) {
            $id = is_numeric($_GET['id']) ? $_GET['id'] : 0;
            $group = $this->userGroupModel->getUserGroupById($id);
            if(!empty($group))
                return $this->render($view, $group);
            else 
                $this->setError('Group Not Found');
        }else{
            $this->setError('Group Not Found');
        }
        $this->render($view);
    }

    /*
    =========================
    =   delete user group   =
    =========================
    * admin can delete user grouop
    */
    private function deleteusergroup($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usergroupId = (int)$_POST['usergroup'];
            $usergroup = $this->userGroupModel->getUserGroupById(($usergroupId));
            if(is_array($usergroup) && !empty($usergroup)){
                if($this->userGroupModel->deleteUserGroup(($usergroupId)))
                    $this->setSuccess('Usergroup Deleted Successfully');
                else
                    $this->setError($this->userGroupModel->getError());
            }else
                $this->setError('Usergroup Not Found');
        }
        Redirect::redirect('usersgroups.php');
    }

    /*
    =========================
    =   Courses Functions   =
    =========================
    */

    /*
    =====================
    =   get courses     =
    =====================
    * get all courses
    * or get category courses
    * or get instructor courses
    */
    private function courses($view)
    {
        $coursesModel = new coursesModel();
        $data = array();
        $courses = $coursesModel->getCourses();
        if (is_array($courses) && !empty($courses)){
            $courses = do_filter('display_courses', $courses);
            $data['courses'] = $courses;
        } else {
            $this->setError('No courses Found');
            return $this->render($view);
        } 
        $this->render($view, $data);
    }

    /* 
    =====================
    =   delete course   =
    =====================
    * admin can delete course
    */
    private function deletecourse($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $coursesModel = new coursesModel();
            $courseId = (int)$_POST['course'];
            $course = $coursesModel->getCourseById($courseId);
            if(is_array($course) && !empty($course)) {
                if($coursesModel->deleteCourse($courseId))
                    $this->setSuccess('Course Deleted Successfully');
                else
                    $this->setError($coursesModel->getError());
            } else 
                $this->setError('Course Not Found');
        }
        return Redirect::redirect('courses.php');
    }

    /* 
    =========================
    =   get course lessons  =
    =========================
    * admin can see course lessons
    */
    private function courselessons($view)
    {
        $coursesModel = new coursesModel();
        $courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $course = $coursesModel->getCourseById($courseId);
        if(is_array($course) && !empty($course)) {
            $courseLessonsModel = new coursesLessonsModel();
            $courseLessons = $courseLessonsModel->getCourseLessons($courseId);
            if(is_array($courseLessons) && !empty($courseLessons)) 
                return $this->render($view, ['lessons' => $courseLessons]);
            else 
                $this->setError('No Lessons added for this course Yet');
        } else
            $this->setError('Course Not Found');
        $this->render($view);
    }

    /* 
    =====================
    =   delete lesson   =
    =====================
    * admin has the permision to delete lesson
    */
    private function deletelesson($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessonId = (int)$_POST['lesson'];
            $courseLessonsModel = new coursesLessonsModel();
            $lesson = $courseLessonsModel->getLessonById($lessonId);
            if (is_array($lesson) && !empty($lesson)) {
                if ($courseLessonsModel->deleteCourseLesson($lessonId))
                    $this->setSuccess('Lesson Deleted Successfully');
                else
                    $this->setError($courseLessonsModel->getError());
            } else 
                $this->setError('Lesson Not Found');
        }
        Redirect::redirect('courses.php');
    }

}
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
        $id = isset($_GET) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
        if($id > 0){
            $category = $this->catModel->getCategoryById($id);
            if(!empty($category)){
                if($this->catModel->deleteCategory($id)){
                    $this->setSuccess('Category Deleted Successfully');
                    return Redirect::redirect('categories.php');
                } else
                    $this->setError($this->catModel->getError());
            } else
                $this->setError('Category Not Found');
            
        }else
            $this->setError('Category Not Found');
        $this->render($view);
    }

    /* 
    =====================
    =   users function  =
    =====================
    * return all users
    */
    private function users($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            if($this->usersModel->deleteUser($id)){
                $this->setSuccess('User Deleted Successfully');
                Redirect::redirect('users.php');
            }else{
                $this->setError($this->userGroupModel->getError());
                Redirect::redirect('users.php');
            }
        }else{
            $users = $this->usersModel->getUsers();
            $this->render($view, $users);
        }
        
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
            }elseif(strlen($email < 10)){
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

    /* 
    =============================
    =   users groups functions  =
    =============================
    * if request = post => delete user group
    * if request = get => return all user group
    */
    private function usersgroups($view)
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            if($this->userGroupModel->deleteUserGroup($id)){
                $this->setSuccess('Group Deleted Successfully');
                Redirect::redirect('usersgroups.php');
            }else{
                $this->setError($this->userGroupModel->getError());
                Redirect::redirect('usersgroups.php');
            }
        }else{
            $usersGroups = $this->userGroupModel->getUserGroups();
            $this->render($view, $usersGroups);
        }
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

}
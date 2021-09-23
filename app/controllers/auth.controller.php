<?php

class Auth extends Controller
{

    public function login()
    {
        if(isLogin()){
            // echo '<br>';
            // var_dump($_SESSION['user']);
            invalidRedirect();
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $password = hashPassword($password);
            $user = new usersModel();
            $login = $user->login($username,$password);
            if($login){
                $userData = $user->getUserData();
                $_SESSION['user'] = $userData;
                    if($userData['group_id'] == 1){
                        Redirect::redirect('admin/index.php');
                    }elseif($userData['group_id'] == 2){
                        Redirect::redirect('instructor/index.php');
                    }elseif($userData['group_id']){
                        Redirect::redirect('front/index.php');
                    }
            }else{
                $this->setError($user->getError());
                include VIEWS . DS . 'front' . DS . 'login.html';
            }
        }else{
            include VIEWS . DS . 'front' . DS . 'login.html';
        }
    }
}